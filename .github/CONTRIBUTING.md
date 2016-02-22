# Contributing

Contributions are **welcome** and will be fully **credited**.

We accept contributions via Pull Requests on [Github](https://github.com/refinery29/piston).

If you're not able to create a Pull Request, feel free to open up on issue to address any problem, or propose new features you'd like to see.

## Pull Requests

- **[PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)** - The easiest way to apply the conventions is to install [PHP Code Sniffer](http://pear.php.net/package/PHP_CodeSniffer).

- **Add tests!** - Your patch won't be accepted if it doesn't have tests.

- **Document any change in behaviour** - Make sure the README and any other relevant documentation are kept up-to-date.

- **Consider our release cycle** - We try to follow semver. Randomly breaking public APIs is not an option.

- **Create topic branches** - Don't ask us to pull from your master branch.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please squash them before submitting.

## Fixing CS Issues

Run

```
$ make cs
```

## Running Tests

Run

```
$ make test
```

Run

```
$ make coverage
```

if you are interested in code coverage.

Run

```
$ make humbug
```

if you are interested in running mutation tests.

## Extra lazy?

Run

```
$ make
```

to run both coding standards check and tests!

**Happy coding**!
