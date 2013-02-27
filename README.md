Vaultage
========

Securize your files easily by defined a key on local machine. 
A passphrase can increase security.

You'll can version your files with criticals data and decrypt these ones on distant server.

## Installation

`wget https://github.com/rezzza/vaultage/raw/master/vaultage.phar`

## Default configuration

Default filename is `.vaultage.json`:

```json
{
    "key": "file://~/.vaultage/your_project.key",
    "passphrase": true,
    "files": {
        "app/config/parameters.yml": "app/config/parameters.yml.crypted"
    }
}
```

## Commands

```sh
$ ./vaultage.phar encrypt
$ ./vaultage.phar decrypt
```

There is many options on commands:

- **configuration-file**: Define a specific configuration file (default is .vaultage.json)
- **files**: Apply encryption/decryption on defined file(s), you can define many file by using comma separator
- **verbose**: Output the encrypted/decrypted data.
- **write**: Write on destinary file

## Todo

- Initialization command
- Write tests
- Documentation
