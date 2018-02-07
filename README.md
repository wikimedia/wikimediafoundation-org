# Wikimedia Foundation

## Usage

Add to provisioner:

```
wmfoundation:
repo: git@github.com:reaktivstudios/vvv-vip-go.git
hosts:
	- wmfoundation.test
	- es.wmfoundation.test
	- de.wmfoundation.test
custom:
	wp_type: subdomain
	vip_repo: git@github.com:reaktivstudios/wmfoundation.git
```
