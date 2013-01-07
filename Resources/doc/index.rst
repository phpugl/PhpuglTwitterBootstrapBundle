PhpuglTwitterBootstrapBundle
============================

Introduction
------------

PhpuglTwitterBootstrapBundle is a configurable Twitter bootstrap bundle. It compiles with the help of *leafo/lessphp*
the Twitter bootstrap less file to stylesheet. The difference to other bundles is, that it is simple to use and it allows to
modify the *variables.less* via config.yml.

Installation
------------

1. Add this bundle to your composer.json

You can install this bundle using composer

.. code-block :: bash

    composer require --no-update phpugl/twitter-bootstrap-bundle:master

or add the package to your ``composer.json`` file directly.

.. code-block :: javascript

   {
       "require": {
           "phpugl/twitter-bootstrap-bundle": "dev-master",
       }
   }

2. Extend the ``AppKernel.php``

.. code-block :: php

    // in AppKernel::registerBundles()
        $bundles = array(
            // ...
            new Phpugl\TwitterBootstrapBundle\PhpuglTwitterBootstrapBundle(),
            // ...
        );

3. Run composer install.

.. code-block :: bash

    composer -v install


Usage
-----

Compile files
~~~~~~~~~~~~~

PhpuglTwitterBootstrapBundle requires no initial configuration to get you started. You can compile and generate the files
with this command.

.. code-block :: bash

    app/console twitter-bootstrap:compile

If you want to modify some variables for the compiled stylesheets you have the oppertunity to add some configuration.

.. code-block :: yaml

    # config.yml
    phpugl_twitter_bootstrap:
        less:
            variables:
                sansFontFamily: "Arial, Helvetica, sans-serif"
                gridColumns:    10
                ...

You must use the variable keys from the [`variables.less`](https://github.com/twitter/bootstrap/blob/master/less/variables.less) .
The compiler script will replace the old value by the new one.

Insert Assets
~~~~~~~~~~~~~

After compiling you can use the files as assets in your layout.


.. code-block :: twig

    #layout.html.twig
    <html>
        <head>
            <!-- Stylesheets -->
            <link href="{{ asset('bundles/phpugltwitterbootstrap/css/bootstrap.css') }}" rel="stylesheet">

        </head>
        <body>
            <!-- your content -->

            <!-- Stylesheets -->
            {% javascripts
                  '@PhpuglTwitterBootstrapBundle/Resources/public/js/bootstrap.js'
            %}
            <script type="text/javascript" src="{{ asset_url }}"></script>
            {% endjavascripts %}
        </body>
    </html>

Override form fields template
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Not finished yet.

.. code-block :: yaml

    twig:
        form:
            resources:
                - 'PhpuglTwitterBootstrapBundle:Form:bootstrap.html.twig'


Default configuration
---------------------

.. code-block :: javascript

    twitter_bootstrap:
        less:
            out: "bootstrap.css"
            files:
                - "bootstrap.less"
                - "responsive.less"
            variables: ~
        images:
            files:
                - "glyphicons-halflings.png"
                - "glyphicons-halflings-white.png"
        javascript:
            out: "bootstrap.js"
            files:
                - "bootstrap-transition.js"
                - "bootstrap-alert.js"
                - "bootstrap-modal.js"
                - "bootstrap-dropdown.js"
                - "bootstrap-scrollspy.js"
                - "bootstrap-tab.js"
                - "bootstrap-tooltip.js"
                - "bootstrap-popover.js"
                - "bootstrap-button.js"
                - "bootstrap-collapse.js"
                - "bootstrap-carousel.js"
                - "bootstrap-typeahead.js"
                - "bootstrap-affix.js"

License
-------

Copyright (c) 2012 PHPUGL

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
