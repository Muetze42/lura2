wip

Command line interface tool for **personal** usage to create new Laravel projects.  
There are a few ideas on how to customize this tool for yourself. But if and when I implement this depends on my time
and desire.

The available "features" can be found in the [src/Features](/src/Features) directory.

![01](/docs/assets/01.png?v=d7a2118ac99dee6d494b40e3e788b450)
![02](/docs/assets/02.png?v=4320b908207b3cf8bca84fa7ae11cf24)
![03](/docs/assets/03.png?v=9c5dac6b2b4fc7f555b63e06b098e443)

# Install

Remove all old Lura packages if installed

```shell
composer global remove norman-huth/lura norman-huth/laravel-installer norman-huth/advanced-laravel-installer norman-huth/package-init norman-huth/lura-laravel-localize
```

Install Lura 2

```shell
composer global require norman-huth/lura2:"@dev"
```

Execute Lura

```shell
lura
```

## Notice

Laravel is a Trademark of Laravel Holdings Inc.
