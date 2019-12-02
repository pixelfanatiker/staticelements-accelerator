<?php
/*
 * StaticElements Accelerator
 *
 * Copyright 2015 by Florian Gutwald
 * <florian@frontend-mercenary.com>
 *
 * StaticElements Accelerator is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * StaticElements Accelerator is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * StaticElements Accelerator; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package staticelements-accelerator
 */

// Common
$_lang['seaccelerator.title'] = 'StaticElements Accelerator';
$_lang['seaccelerator.description'] = 'A management cockpit for static elements';


// Tabs
$_lang['seaccelerator.tab_files'] = "Files";
$_lang['seaccelerator.tab_files.description'] = "Synchronizes static elements and watches folders about changed files";
$_lang['seaccelerator.tab_chunks'] = "Chunks";
$_lang['seaccelerator.tab_snippets'] = "Snippets";
$_lang['seaccelerator.tab_templates'] = "Templates";
$_lang['seaccelerator.tab_plugins'] = "Plugins";


// File actions
$_lang['seaccelerator.files.description'] = "Synchronizes static elements and watches folders about changed files";
$_lang['seaccelerator.files.actions.quickupdate'] = "Quick edit file";
$_lang['seaccelerator.files.actions.generate_all'] = "Process all files to static element";
$_lang['seaccelerator.files.actions.sync_all'] = "Sync all files";
$_lang['seaccelerator.files.actions.create.processing'] = 'Creating elements...';

$_lang['seaccelerator.files.actions.create'] = "Create element ";
$_lang['seaccelerator.files.actions.edit_file'] = "Edit file";
$_lang['seaccelerator.files.actions.delete_element'] = "Delete file and element";
$_lang['seaccelerator.files.actions.delete_file'] = "Delete file";

$_lang['seaccelerator.files.actions.delete.confirm.title'] = " Delete file";
$_lang['seaccelerator.files.actions.delete.confirm.text'] = "Finally delete the selected file?";

$_lang['seaccelerator.files.actions.create_element'] = "Create new element";
$_lang['seaccelerator.files.actions.create_element_confirm'] = "Do you wan't to create a new element from the selected file?";


// Element bar
$_lang['seaccelerator.elements.static_file'] = "Path";
$_lang['seaccelerator.elements.modClass'] = "Element type";
$_lang['seaccelerator.elements.source_id'] = "Source ID";
$_lang['seaccelerator.elements.static'] = "Static";

$_lang['seaccelerator.elements.filter_by_type'] = "Filter categories";
$_lang['seaccelerator.elements.filter_by_name'] = "Filter name";


// Element actions
$_lang['seaccelerator.elements.actions.quickupdate'] = "Edit element ";

$_lang['seaccelerator.elements.actions.export_all'] = "Export all elements";
$_lang['seaccelerator.elements.actions.tostatic.all.confirm.title'] = "Export all elements?";
$_lang['seaccelerator.elements.actions.tostatic.all.confirm.text'] = "Alle elements get static and will be saved as file on your server.";

$_lang['seaccelerator.elements.actions.static'] = "Save as static element ";
$_lang['seaccelerator.elements.actions.static.restore'] = "Restore file from element";

$_lang['seaccelerator.elements.actions.sync.tofile'] = "Sync from element to file";
$_lang['seaccelerator.elements.actions.sync.fromfile'] = "Update element with content from file";

$_lang['seaccelerator.elements.actions.restore.tofile'] = "Restore file with content from element";
$_lang['seaccelerator.elements.actions.restore.tofile.confirm.title'] = "Restore missing file";
$_lang['seaccelerator.elements.actions.restore.tofile.confirm.text'] = "The missing file will be restored with content from the element.";

$_lang['seaccelerator.elements.actions.sync.tofile.confirm.title'] = "Sync element to file";
$_lang['seaccelerator.elements.actions.sync.tofile.confirm.text'] = "The file will be updated with content from the element.";

$_lang['seaccelerator.elements.actions.sync.fromfile.confirm.title'] = "Syn file to element";
$_lang['seaccelerator.elements.actions.sync.fromfile.confirm.text'] = "The element will be updated with the files' content from the server.";

$_lang['seaccelerator.elements.actions.static.confirm.title'] = "Set as static element";
$_lang['seaccelerator.elements.actions.static.confirm.text'] = "Do you wan't to set the selected element as static?";

$_lang['seaccelerator.elements.actions.delete'] = "Delete element";
$_lang['seaccelerator.elements.actions.delete.confirm.title'] = "Delete element";
$_lang['seaccelerator.elements.actions.delete.confirm.text'] = "Do you wan't to delete the selected element?";

$_lang['seaccelerator.elements.actions.delete_file_element'] = "Delete file and element";
$_lang['seaccelerator.elements.actions.delete_file_element.confirm.title'] = "Delete file and element";
$_lang['seaccelerator.elements.actions.delete_file_element.confirm.text'] = "DDo you wan't to delete the selected element and the file on the server?";


// Element status
$_lang['seaccelerator.elements.status.unchanged'] = "No changes";
$_lang['seaccelerator.elements.status.changed'] = "Changed file";
$_lang['seaccelerator.elements.status.deleted'] = "Deleted file";

$_lang['seaccelerator.elements.element_status.unchanged'] = "No changes";
$_lang['seaccelerator.elements.element_status.changed'] = "Changed file";
$_lang['seaccelerator.elements.element_status.deleted'] = "File is not present";
$_lang['seaccelerator.elements.element_status.not_static'] = "Element is not static";


// Messages
$_lang['seaccelerator.no_permission'] = "No access to see lists!";
$_lang['seaccelerator.no_permission.delete'] = "No access to delete files!";

$_lang['seaccelerator.error.ufg_no_data'] = "No files present!";


// Systemsettings
$_lang['seaccelerator.namespace.settings'] = "Settings";

$_lang['seaccelerator.elements_directory.name'] = "Elements Directory";
$_lang['seaccelerator.elements_directory.description'] = "";

$_lang['seaccelerator.mediasource.name'] = "Media source";
$_lang['seaccelerator.mediasource.description'] = "";

$_lang['seaccelerator.use_categories.name'] = "Use categories";
$_lang['seaccelerator.use_categories.description'] = "";

$_lang['seaccelerator.element_type_separation.name'] = "Element sorting";
$_lang['seaccelerator.element_type_separation.description'] = "Defines, how files get related to elements. Standard is folder. (More options will come)";

$_lang['seaccelerator.element_type_rules.name'] = "Element-Sortierungs Regel";
$_lang['seaccelerator.element_type_rules.description'] = "Relation rule from modClass to file. Separated by comma, eg. modClass:rule. Standard is modChunk:chunks,modSnippet:snippets,modTemplate:templates,modPlugin:plugins";
