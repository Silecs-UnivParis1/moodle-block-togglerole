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

Like any normal block plugin, you should then insert a block instance
in a Moodle page. Unfortunately, making the block is visible for every teacher on every course
is quite tricky (I think *insane* would be the right word) with Moodle:

As an admin,

- Go to the site main page
- Switch to editing mode
- Use the drop-down list to select this block plugin
- Optionaly, move the block to the right position
- configure the block to make it visible on every page
- Go to a course page
- configure the block to make it visible on any course page, or any course sub-page

(C) 2016-2020 Silecs 

