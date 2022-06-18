#!/bin/bash

mkdir libs
cp node_modules/wikipedia-preview/dist/wikipedia-preview.production.js libs/
cp node_modules/wikipedia-preview/dist/wikipedia-preview-link.css libs/
npm run build
