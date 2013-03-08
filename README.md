# Vaultage

Securize your files easily by defining a key on local machine. A passphrase can
increase security.

You'll be able to version your files containing criticals data and decrypt them
on a remote server.

## Installation

`wget --no-check-certificate https://github.com/rezzza/vaultage/raw/master/vaultage.phar`

**or**

`curl -O -sL https://github.com/rezzza/vaultage/raw/master/vaultage.phar`

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
$ ./vaultage.phar self-update
```

Commands have the following options:

- **configuration-file**: define a specific configuration file (default is
  `.vaultage.json`);
- **files**: apply encryption or decryption on defined file(s), you can define
  many file by using comma separator;
- **verbose**: output the encrypted/decrypted data;
- **write**: write on the output file.

## Todo

- Initialization command
- Write tests
- Documentation
