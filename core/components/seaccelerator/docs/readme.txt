StaticElements Accelerator for MODX

Author: Florian Gutwald <florian@frontend-mercenary.com>

--------------------------------------------------------

Description

Easy handling of static files and elements.
Single and multiple processing from files to MODX.
Use a mediasource and structure your elements in MODX like on your filesystem or repository.

After installing the package, the standard media source for your elements is the default mediasource from MODX.
As default directory is set "elements". So elements will be found in modx_base_url/elements/

The standard folder structure where StaticElements Accelerator is looking for files:

modx_base_url
-- elements
---- chunks
---- snippets
---- templates
---- plugins

This version is only able to relate files via folders.
You can define your own rules with this scheme: modChunk:chunks,modSnippet:snippets,modTemplate:templates,modPlugin:plugins

--------------------------------------------------------

Please report ideas and bugs to https://github.com/pixelfanatiker/staticelements-accelerator/issues.
Watch out for the next update.
