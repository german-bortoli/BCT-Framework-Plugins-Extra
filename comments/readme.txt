
@file
Generic comment plugin.

This plugin provides functions for commenting on data items, and it works by extending specified item
views with a comment data item and any comments previously listed.

@note Populate $CONFIG->comments_on_items with a list of data types, requires that an item has a valid GUID
in order to map comments correctly.

Alternatively, you can add comments manually by displaying the input/comment view and passing an object
which you wish to comment on.

@package comments
@license The MIT License (see LICENCE.txt), other licenses available.
@author Marcus Povey <marcus@marcus-povey.co.uk>
@copyright Marcus Povey 2009-2012
@link http://www.marcus-povey.co.uk

