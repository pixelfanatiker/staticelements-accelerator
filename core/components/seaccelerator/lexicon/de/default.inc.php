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
$_lang['seaccelerator.description'] = 'Verwaltung für statische Elemente';


// Tabs
$_lang['seaccelerator.tab_files'] = "Dateien";
$_lang['seaccelerator.tab_files.description'] = "Synchronisiert statische Elemente und überwacht Ordner auf geänderte Dateien";
$_lang['seaccelerator.tab_chunks'] = "Chunks";
$_lang['seaccelerator.tab_snippets'] = "Snippets";
$_lang['seaccelerator.tab_templates'] = "Templates";
$_lang['seaccelerator.tab_plugins'] = "Plugins";


// File actions
$_lang['seaccelerator.files.description'] = "Synchronisiert statische Elemente und überwacht Ordner auf geänderte Dateien";
$_lang['seaccelerator.files.actions.quickupdate'] = "Datei schnell bearbeiten";
$_lang['seaccelerator.files.actions.generate_all'] = "Alle Dateien verarbeiten";
$_lang['seaccelerator.files.actions.sync_all'] = "Alle Dateien Synchronisieren";
$_lang['seaccelerator.files.actions.create.processing'] = 'Erstelle Elemente...';

$_lang['seaccelerator.files.actions.create'] = "Element erstellen";
$_lang['seaccelerator.files.actions.edit_file'] = "Datei bearbeiten";
$_lang['seaccelerator.files.actions.delete_element'] = "Datei und Element löschen";
$_lang['seaccelerator.files.actions.delete_file'] = "Datei löschen";

$_lang['seaccelerator.files.actions.delete.confirm.title'] = " Datei löschen";
$_lang['seaccelerator.files.actions.delete.confirm.text'] = "Die ausgewählte Dateie wird endgültig löschen?";

$_lang['seaccelerator.files.actions.create_element'] = "Neues Element erstellen";
$_lang['seaccelerator.files.actions.create_element_confirm'] = "Aus der ausgewählten Datei ein Element erstellen?";


// Element bar
$_lang['seaccelerator.elements.static_file'] = "Pfad";
$_lang['seaccelerator.elements.static'] = "Statisch";

$_lang['seaccelerator.elements.filter_by_type'] = "Filter nach Kategorie";
$_lang['seaccelerator.elements.filter_by_name'] = "Filter nach Name";


// Element actions
$_lang['seaccelerator.elements.actions.quickupdate'] = "Element bearbeiten";

$_lang['seaccelerator.elements.actions.export_all'] = "Alle Elemente exportieren";
$_lang['seaccelerator.elements.actions.tostatic.all.confirm.title'] = "Elemente exportieren?";
$_lang['seaccelerator.elements.actions.tostatic.all.confirm.text'] = "Alle Elemente werden statisch und auf dem Server gespeichert.";

$_lang['seaccelerator.elements.actions.static'] = "Als statisches Element speichern";
$_lang['seaccelerator.elements.actions.static.restore'] = "Datei aus Datenbank wiederherstellen";

$_lang['seaccelerator.elements.actions.sync.tofile'] = "Von Element zu File synchronisieren";
$_lang['seaccelerator.elements.actions.sync.fromfile'] = "Element mit Inhalt von Datei synchronisieren";

$_lang['seaccelerator.elements.actions.restore.tofile'] = "Datei mit Inhalt von Element wiederherstellen";
$_lang['seaccelerator.elements.actions.restore.tofile.confirm.title'] = "Datei wieder herstellen";
$_lang['seaccelerator.elements.actions.restore.tofile.confirm.text'] = "Die Fehlende Datei wird mit dem Inhalt des aktuellen Element wiederhergestellt.";

$_lang['seaccelerator.elements.actions.sync.tofile.confirm.title'] = "Element zu File Synchronisation";
$_lang['seaccelerator.elements.actions.sync.tofile.confirm.text'] = "Das File auf dem Server wird mit dem Inhalt des Elements aktualisiert.";

$_lang['seaccelerator.elements.actions.sync.fromfile.confirm.title'] = "File zu Element Synchronisation";
$_lang['seaccelerator.elements.actions.sync.fromfile.confirm.text'] = "Das Element wird mit dem Inhalt des Files vom Server aktualisiert.";

$_lang['seaccelerator.elements.actions.static.confirm.title'] = "Als statisches Element festlegen";
$_lang['seaccelerator.elements.actions.static.confirm.text'] = "Das ausgewählte Element als statisches Element festlegen?";

$_lang['seaccelerator.elements.actions.delete'] = "Element löschen";
$_lang['seaccelerator.elements.actions.delete.confirm.title'] = "Element löschen";
$_lang['seaccelerator.elements.actions.delete.confirm.text'] = "Das ausgewählte Element wirklich löschen?";

$_lang['seaccelerator.elements.actions.delete_file_element'] = "Datei und Element löschen";
$_lang['seaccelerator.elements.actions.delete_file_element.confirm.title'] = "Datei und Element löschen";
$_lang['seaccelerator.elements.actions.delete_file_element.confirm.text'] = "Datei und Element endgültig löschen?";


// Element status
$_lang['seaccelerator.elements.status.unchanged'] = "Keine Änderungen";
$_lang['seaccelerator.elements.status.changed'] = "Datei wurde geändert";
$_lang['seaccelerator.elements.status.deleted'] = "Datei wurde gelöscht";

$_lang['seaccelerator.elements.element_status.unchanged'] = "Keine Änderungen";
$_lang['seaccelerator.elements.element_status.changed'] = "Datei geändert";
$_lang['seaccelerator.elements.element_status.deleted'] = "Datei nicht vorhanden";
$_lang['seaccelerator.elements.element_status.not_static'] = "Element nicht statisch";


// Messages
$_lang['seaccelerator.no_permission'] = "Keine Berechtigung um Listen zu sehen!";
$_lang['seaccelerator.no_permission.delete'] = "Keine Berechtigung zum Löschen von Dateien!";

$_lang['seaccelerator.error.ufg_no_data'] = "Keine Daten vorhanden!";


// Systemsettings
$_lang['seaccelerator.namespace.settings'] = "Settings";

$_lang['seaccelerator.elements_directory.name'] = "Elements Directory";
$_lang['seaccelerator.elements_directory.description'] = "";

$_lang['seaccelerator.mediasource.name'] = "Medienquelle";
$_lang['seaccelerator.mediasource.description'] = "";

$_lang['seaccelerator.use_categories.name'] = "Benutze Kategorien";
$_lang['seaccelerator.use_categories.description'] = "";

$_lang['seaccelerator.element_type_separation.name'] = "Element-Sortierung";
$_lang['seaccelerator.element_type_separation.description'] = "Definiert, wie Dateien Element-Typen zugeordnet werden. Standard ist folder.";

$_lang['seaccelerator.element_type_rules.name'] = "Element-Sortierungs Regel";
$_lang['seaccelerator.element_type_rules.description'] = "Regel der Zuordnung von modClass zu Datei. Kommasepariert, modClass:rule. Standard ist modChunk:chunks,modSnippet:snippets,modTemplate:templates,modPlugin:plugins";
