# Vaultage

Securize your files easily by defining a key on local machine. A passphrase can
increase security.

You'll be able to version files containing critical data.

Fits well with automated deployment tools.

## Installation

`wget --no-check-certificate https://github.com/rezzza/vaultage/raw/master/vaultage.phar`

**or**

`curl -O -sL https://github.com/rezzza/vaultage/raw/master/vaultage.phar`

## Commands

```sh
$ ./vaultage.phar init
$ ./vaultage.phar self-update

$ ./vaultage.phar encrypt
$ ./vaultage.phar encrypt --files=myfile.yml
$ ./vaultage.phar decrypt
$ ./vaultage.phar decrypt --files=myfile.yml.gpg

$ ./vaultage.phar diff --files=a.gpg,b.gpg
$ ./vaultage.phar diff --files=a.yml,a.gpg
```

### Options
- **configuration-file**: define a specific configuration file (default is
  `.vaultage.json`).
- **files**: apply encryption or decryption on defined file(s), you can define
  many file by using comma separator.
- **verbose**: output the encrypted/decrypted data.
- **write**: write on the output file.

## Crypto Backends

- [Basic](doc/backend_basic.md): packed by default, without any requirements.
- [GPG](doc/backend_gpg.md): for serious guys ;)

## Capifony integration

Just hook in the right place with this kind of recipe:

```ruby
before "deploy:share_childs" do
    origin_file = "app/config/parameters/"+rails_env+".yml"
    system "vaultage decrypt --write"

    destination_file = latest_release + "/app/config/parameters.yml" # Notice the
    latest_release
    top.upload(origin_file, destination_file)
end
```

## Todo

- Write tests
