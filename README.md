<img src="docs/logo.png" width="150" alt="logo"/>

# Contao Altcha Antispam

This is a Contao implementation of [Altcha](https://altcha.org/). The extension provides a front end form field for the Contao form generator.

<img src="docs/frontend.png" alt="logo" width="400"/>

## Installation

You can install the package via composer:

```bash
composer require markocupic/contao-altcha-antispam
```

## Configuration and usage

In your `config/config.yaml`, you can set the following variables. The `hmac_key` is required.

```yaml
markocupic_contao_altcha_antispam:
    hmac_key: 'sdfsadZUI#!@sfdssf321231' # required
    algorithm: 'SHA-256' # optional allowed: 'SHA-256', 'SHA-512' or 'SHA-384'
    range_min: 1000 # optional
    range_max: 100000 #optional
```

Out of the box, the extension will use the `/_contao_altcha_challenge` endpoint to get the challenges.
