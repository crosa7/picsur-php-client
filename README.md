# Picsur PHP Client

A simple php client for [@CaramelFur/Picsur](https://github.com/CaramelFur/Picsur) image service. Currently it only supports upload and delete.
The other possible actions will be implemented in the future or on request.

## Usage

```
$picsurClient = new PicsurClient('your-api-key', 'http://your-host', 1234); (port nor required)

// For upload
$picsurClient->upload('path/to/your-image');

// For delete
$picsurClient->delete(['your-image-id', 'another-image-id']);
```
