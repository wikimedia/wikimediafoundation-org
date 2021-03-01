#!/bin/bash -e
#
# Deploy the current branch to VIP Go.
#
# Based on the vip-go-builder script from https://github.com/Automattic/vip-go-build
#

set -ex

# The deploy suffix flexibility is mainly here to allow
# us to test Circle and Travis builds simultaneously on
# the https://github.com/Automattic/vip-go-skeleton/ repo.
DEPLOY_SUFFIX="${VIP_DEPLOY_SUFFIX:--built}"
BRANCH="${GITHUB_REF#refs/heads/}"
COMMIT_SHA=${GITHUB_SHA}

SRC_DIR="${PWD}"
BUILD_DIR="/tmp/vip-go-build-$(date +%s)"
DEPLOY_BRANCH="${BRANCH}${DEPLOY_SUFFIX}"

COMMIT_AUTHOR_NAME="$( git log --format=%an -n 1 ${COMMIT_SHA} )"
COMMIT_AUTHOR_EMAIL="$( git log --format=%ae -n 1 ${COMMIT_SHA} )"
COMMIT_COMMITTER_NAME="$( git log --format=%cn -n 1 ${COMMIT_SHA} )"
COMMIT_COMMITTER_EMAIL="$( git log --format=%ce -n 1 ${COMMIT_SHA} )"
COMMIT_MESSAGE="$( git log --format=%B -n 1 ${COMMIT_SHA} | sed -r 's| #(\d*)| '${SRC_REPO}'#\1|' )"

# Everything seems OK, getting the built repo sorted
# --------------------------------------------------

echo "Deploying ${BRANCH} to ${DEPLOY_BRANCH}"

# Making the directory we're going to sync the build into
git init "${BUILD_DIR}"
cd "${BUILD_DIR}"
git remote add origin "https://git:${DEPLOY_TOKEN}@github.com/${DEPLOY_REPO}.git"
if [[ 0 = $(git ls-remote --heads origin "${DEPLOY_BRANCH}" | wc -l) ]]; then
	echo -e "\nCreating a ${DEPLOY_BRANCH} branch..."
	git checkout --quiet --orphan "${DEPLOY_BRANCH}"
else
	echo "Using existing ${DEPLOY_BRANCH} branch"
	git fetch origin "${DEPLOY_BRANCH}" --depth=1
	git checkout --quiet "${DEPLOY_BRANCH}"
fi

# Copy the files over
# -------------------

if ! command -v 'rsync'; then
	# @FIXME Probably there's a way we could check if APT is up to date or not
	# so we don't have to run update every time
	sudo apt-get update
	sudo apt-get install -q -y rsync
fi

echo "Syncing files... quietly"

rsync --delete -a "${SRC_DIR}/" "${BUILD_DIR}" --exclude='.git/'

# gitignore override
# To allow commiting built files in the build branch (which are typically ignored)
# -------------------

BUILD_DEPLOYIGNORE_PATH="${BUILD_DIR}/.deployignore"
if [ -f $BUILD_DEPLOYIGNORE_PATH ]; then
	BUILD_GITIGNORE_PATH="${BUILD_DIR}/.gitignore"

	if [ -f $BUILD_GITIGNORE_PATH ]; then
		rm $BUILD_GITIGNORE_PATH
	fi

	echo "-- found .deployignore; emptying all gitignore files"
	find $BUILD_DIR -type f -name '.gitignore' | while read GITIGNORE_FILE; do
		echo "# Emptied by vip-go-build; '.deployignore' exists and used as global .gitignore. See https://wp.me/p9nvA-89A" > $GITIGNORE_FILE
		echo "${GITIGNORE_FILE}"
	done

       	echo "-- using .deployignore as global .gitignore"
	mv $BUILD_DEPLOYIGNORE_PATH $BUILD_GITIGNORE_PATH
fi

# Make up the commit, commit, and push
# ------------------------------------

# Set Git committer
git config user.name "${COMMIT_COMMITTER_NAME}"
git config user.email "${COMMIT_COMMITTER_EMAIL}"

# Add changed files, delete deleted, etc, etc, you know the drill
git add -A .

if [ -z "$(git status --porcelain)" ]; then
	echo "NOTICE: No changes to deploy"
	exit 0
fi

# Commit it.
MESSAGE=$( printf 'Build changes from %s\n\n%s' "${COMMIT_SHA}" "${COMMIT_MESSAGE}" )
# Set the Author to the commit (expected to be a client dev) and the committer
# will be set to the default Git user for this CI system
git commit --author="${COMMIT_AUTHOR_NAME} <${COMMIT_AUTHOR_EMAIL}>" -m "${MESSAGE}"

# Push it (push it real good).
git push origin "${DEPLOY_BRANCH}"
