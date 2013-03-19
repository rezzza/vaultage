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

[Back to home](/README.md)
