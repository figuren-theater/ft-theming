<!-- PROJECT LOGO -->
<br />
<div align="center">
  <a href="https://github.com/figuren-theater/ft-theming">
    <img src="https://raw.githubusercontent.com/figuren-theater/logos/main/favicon.png" alt="figuren.theater Logo" width="100" height="100">
  </a>

  <h1 align="center">figuren.theater | Theming</h1>

  <p align="center">
    This package helps you & your brand with a consistent look across the <a href="https://figuren.theater">figuren.theater</a> multisite network and beyond.
    <br /><br /><br />
    <a href="https://meta.figuren.theater/blog"><strong>Read our blog</strong></a>
    <br />
    <br />
    <a href="https://figuren.theater">See the network in action</a>
    •
    <a href="https://mein.figuren.theater">Join the network</a>
    •
    <a href="https://websites.fuer.figuren.theater">Create your own network</a>
  </p>
</div>

## About


This is the long desc

* [x] *list closed tracking-issues or `docs` files here*
* [ ] Do you have any [ideas](https://github.com/figuren-theater/ft-theming/issues/new) ?

## Background & Motivation

...

## Install

1. Install via command line
	```sh
	composer require figuren-theater/ft-theming
	```

## Usage

### API

```php
Figuren_Theater::API\get_...()
```

## Plugins included

This package contains the following plugins.
Thoose are completely managed by code and lack of their typical UI.

* [WP Better Emails](https://wordpress.org/plugins/wp-better-emails/#developers)

## What does this package do in addition?

Accompaniying the core functionality of the mentioned plugins, theese **best practices** are included with this package.

- [x] Allow any post type to use one or more templates, independently from the theme. This is enabled by default for the `page` post type by using
  ```php
  add_post_type_support(
    'page',
    'post-type-templates',
    [
      'templates' => [
        'blank.php'=>'A blank canvas'
      ],
      'path'=>'ABSPATH_TO_TEMPLATE_DIRECTORY'
    ]
  );
  ```

- [x] Allow third-party scripts to be loaded either `defer`ed or `async` via a filter or a URL#hash.
- [x] Remove jquery 'MIGRATE' console message from frontend.
- [x] Deliver our figuren.theater favicon as fallback, if non is set
- [x] Themed Login using site-icon and theme-colors

## Built with & uses

  - [dependabot](/.github/dependabot.yml)
  - [code-quality](https://github.com/figuren-theater/code-quality/)
     A set of status checks to ensure high and consitent code-quality for the figuren.theater platform.
  - ....

## Contributing

Contributions are what make the open source community such an amazing place to learn, inspire, and create. Any contributions you make are **greatly appreciated**.

If you have a suggestion that would make this better, please fork the repo and create a pull request. You can also simply open an issue with the tag "enhancement".
Don't forget to give the project a star! Thanks again!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request


## Versioning

We use [Semantic Versioning](http://semver.org/) for versioning. For the versions
available, see the [tags on this repository](https://github.com/figuren-theater/ft-theming/tags).

## Authors

  - **Carsten Bach** - *Provided idea & code* - [figuren.theater/crew](https://figuren.theater/crew/)

See also the list of [contributors](https://github.com/figuren-theater/ft-theming/contributors)
who participated in this project.

## License

This project is licensed under the **GPL-3.0-or-later**, see the [LICENSE](/LICENSE) file for
details

## Acknowledgments

  - [altis](https://github.com/search?q=org%3Ahumanmade+altis) by humanmade, as our digital role model and inspiration
  - [@roborourke](https://github.com/roborourke) for his clear & understandable [coding guidelines](https://docs.altis-dxp.com/guides/code-review/standards/)
  - [python-project-template](https://github.com/rochacbruno/python-project-template) for their nice template->repo renaming workflow
