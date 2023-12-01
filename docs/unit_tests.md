# UNIT TESTS

Unit tests are provided for testing the PHP side of the plugin. This primarily means
testing the web services expored by the plugin.

## Configuring Unit Tests

Please see document https://moodledev.io/general/development/tools/phpunit

Currently tests can be run individually, using (for example)

    vendor/bin/phpunit local/gugrades/tests/external/get_add_grade_form_test.php

...or the complete set for the plugin can be executed using

    vendor/bin/phpunit --testsuite local_gugrades_testsuite

## Test configuration

Web service tests, extend the class *gugrades_advanced_testcase*. This creates some basic structure for
tests to use. Including...

* A course
* The 22-point scale
* A teacher
* Some students
* Grade categories (confirming to MyGrades requirements)
* Assignments
* Some grades for the Assignments

## Test descriptions

<dl>
    <dt>[get_activities_test](../tests/external/get_activities_test.php)</dt>
    <dd>Tests the get_activities web service.</dd>
</dl>