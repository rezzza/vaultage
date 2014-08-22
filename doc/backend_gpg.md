# GPG

![image](http://www.gnupg.org/share/logo-gnupg-light-purple-bg.png)

Integrate [gpg](http://www.gnupg.org/)

## Asymmetric mode

Asymmetric mode use public/private key system, you can use `gpg-agent` as daemon on your computer.
Looks at [how to documentation](http://www.gnupg.org/documentation/howtos.en.html) to understand how to add/remove recipients.

You can easily revoke an access to everybody and see who crypte and when file whas crypted.

```json
{
    "backend": "gpg",
    "asymmetric": true,
    "files": [
        "app/config/parameters.yml"
    ],
    "recipients": [
        "John Doe <user@domain.tld>",
        "User 2 <user2@domain.tld>"
    ]
}
```

List of recipients is autocompleted from your `gpg --list-keys` output.

## Symmetric mode

Symetric ask you a passphrase to crypt files. You have to share this passphrase in your team.

```json
{
    "backend": "gpg",
    "asymmetric": false,
    "files": [
        "app/config/parameters.yml"
    ]
}
```

## GPG Usage
1. Generate your key : `gpg --gen-key`
2. Get your key ident: `gpg --list-keys`
3. Send your key to dist server : `gpg --keyserver pgp.mit.edu --send-keys YOURKEYIDENT`
4. Get the key from your friends : `gpg --keyserver pgp.mit.edu --recv-keys FRIENDKEY`
5. Indicate you trust a key: 
```
gpg --edit-key FRIENDKEY
# In shell gpg opened
trust
```

[Back to home](/README.md)
