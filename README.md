# supermercato_24

Tests are implemented for all exercises. Please be aware that are quite naive and not enough for a real world scenario.

Exercise 1
-------------------
Main file is ``exercise_1/src/modules/reverse_binary/app/reverse_binary.js``

- Be sure to have nodejs / npm installed;
- go to ``exercise_1`` dir;
- run ``npm install``;
- run ``npm run build`` (optional, just a build to check that webpack does its magic);
- run ``npm run test`` in order to execute tests.


Exercise 2
-------------------
There's no change_directory.php file as requested by the exercise, because this implementation uses a Path and a FileSystem classes. 
Main files are ``exercise_2/FileSystem/FileSystem.php`` and ``exercise_2/Path/Path``.

- Be sure to have composer and at least PHP 7.2.4 installed;
- go to ``exercise_2`` dir;
- run ``make install``;
- run ``make test`` in order to execute tests.

There is a ``public`` dir in order to quickly test the code with your favourite web server.

Main concern because of lack of time is that low level implementation of FileSystem is tightly coupled with hig level commands and manipulation. Ideally the main data structures (the directories tree and the hash table) should implement a simple interface and be completely black-boxed.


Exercise 3
-------------------
Main file is ``exercise_3/haversine/haversine_coverage.py``

- Be sure to have at least Python 3.5.2 installed;
- go to ``exercise_3`` dir;
- run ``make test`` in order to execute tests.
