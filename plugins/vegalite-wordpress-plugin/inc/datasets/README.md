# Dataset Management

Datasets are handled as CSV strings saved to post meta. While CSVs can be uploaded to WordPress, there is not a clean or clear way to edit the uploaded document. Storing CSV content as meta on the post lets us provide REST endpoints to create or update data, while also letting us expose the raw CSV data through those same endpoints.
