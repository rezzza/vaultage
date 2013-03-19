# Vaultage

Securize your files easily by defining a key on local machine. A passphrase can
increase security.

You'll be able to version your files containing criticals data and decrypt them
on a remote server.

## Installation

`wget --no-check-certificate https://github.com/rezzza/vaultage/raw/master/vaultage.phar`

**or**

`curl -O -sL https://github.com/rezzza/vaultage/raw/master/vaultage.phar`

## Commands

```sh
$ ./vaultage.phar init
$ ./vaultage.phar self-update

$ ./vaultage.phar encrypt
$ ./vaultage.phar decrypt
```

## Backends

[Basic](doc/backend_basic.md)

[GPG](doc/backend_gpg.md)

Commands have the following options:

- **configuration-file**: define a specific configuration file (default is
  `.vaultage.json`);
- **files**: apply encryption or decryption on defined file(s), you can define
  many file by using comma separator;
- **verbose**: output the encrypted/decrypted data;
- **write**: write on the output file.

## Todo

- Write tests
- Documentation
