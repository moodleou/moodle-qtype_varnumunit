Variable numeric set with units question type
https://moodle.org/plugins/qtype_varnumunit

The question type was created by Jamie Pratt (http://jamiep.org/) for
the Open University (http://www.open.ac.uk/).

This version of this question type is compatible with Moodle 3.4+. There are
other versions available for Moodle 2.3+.

This question type can be installed from the Moodle plugins directory using the
link above. You also need to install require Variable numeric sets
(https://moodle.org/plugins/qtype_varnumericset) and Pattern-match
(https://moodle.org/plugins/qtype_pmatch) question types. you should probably also install
https://moodle.org/plugins/editor_ousupsub.

Alternatively, you can install using git:

    git clone git://github.com/moodleou/moodle-qtype_varnumunit.git question/type/varnumunit
    echo /question/type/varnumunit/ >> .git/info/exclude
    git clone git://github.com/moodleou/moodle-qtype_varnumericset.git question/type/varnumericset
    echo /question/type/varnumericset/ >> .git/info/exclude
    git clone git://github.com/moodleou/moodle-qtype_pmatch.git question/type/pmatch
    echo /question/type/pmatch/ >> .git/info/exclude
    git clone git://github.com/moodleou/moodle-editor_ousupsub.git lib/editor/ousupsub
    echo /lib/editor/ousupsub/ >> .git/info/exclude

If the editor is not installed the question type can still be used but if it is
installed when  you make a question that requires scientific notation then this
editor will be shown and a student can either enter an answer with the notation
1x10^5 where the ^5 is expressed with super script.
