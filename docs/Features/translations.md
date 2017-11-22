## Translations Manager

![Admin Architect - Translations Manager](http://docs.adminarchitect.com/images/plugins/translations.png)

Laravel translations come in a Developer friendly format - PHP arrays. But for real customers it's a simple nightmare!
Admin Architect comes with a simple solution that allows your customers change translations any time.

P.S. an event `\Terranet\Administrator\Events\TranslationsChanged` is fired when translations changed.
It allows you to monitor the changes: run some tasks, like export to JS, etc... any time translations changed.