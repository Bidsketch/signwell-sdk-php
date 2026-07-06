# Contributing

This SDK is generated from `Bidsketch/signwell-sdk-generator`.

Do not edit generated runtime files directly in the public SDK repository.
Make changes in the generator repo under `config/languages/php.json`,
`templates/php/`, `extras/php/overlay/`, or `scripts/spec_hooks/php.rb`,
then regenerate the SDK.

Useful commands:

```bash
./scripts/generate.sh php
./scripts/validate-generated.sh php
./scripts/check-template-drift.sh php
```
