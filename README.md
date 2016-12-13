moodle-block-togglerole
=======================

A block module for Moodle that will display to teachers
a simple button to switch to a simpler interface.

It uses Moodle's internal "Switch to role" function.

The block can be configured to switch only in the current course,
or globaly, in every course where the teacher in enrolled.
Global roles, notably admins, cannot use this global switch.

The target role defaults to "teacher (non-editing)",
but this can be configured.


## Installation

Extract files or git-clone them under your Moodle directory,
into `blocks/togglerole`. E.g.

`cd blocks ; git clone ... togglerole`
