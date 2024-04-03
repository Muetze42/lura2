wip

Command line interface tool for **personal** usage created with:

The available "features" can be found in the directory [src/Features](/src/Features).

* [Illuminate Console component](https://laravel.com/docs/11.x/artisan)
* [Laravel Prompts](https://laravel.com/docs/11.x/prompts)
* [Illuminate Processes component](https://laravel.com/docs/11.x/processes)
* [Illuminate Validation component](https://laravel.com/docs/11.x/validation)
* [Illuminate Http component](https://laravel.com/docs/11.x/http-client)
* [Illuminate Filesystem component](https://laravel.com/docs/11.x/filesystem)
* [Illuminate Support component](https://laravel.com/docs/11.x/helpers)

{images}

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
