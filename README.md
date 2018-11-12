# letakol/fiboot

## Dependencies

- AngularJS v1.6.11 [http://angularjs.org](http://angularjs.org)
- Bootstrap v3.3.7 [http://getbootstrap.com](http://getbootstrap.com)
- jQuery v3.2.1 [https://blog.jquery.com/](https://blog.jquery.com/)
- jQuery UI v1.12.1 [http://jqueryui.com](http://jqueryui.com)
- CodeMirror [http://codemirror.net](http://codemirror.net)
- vis.js v4.20.1 [https://github.com/almende/vis](https://github.com/almende/vis)

## Project architecture

```
/_               (libraries)
/api             (php BO)
/classes         (js models)
/components      (usable components)
/controllers     (app core)
/services        (app services)
/views           (pages)
 index.html
```

## Git convention

### Commit message

> :emoji: commit message [issue #]

### Emoji list

|Emoji|Meaning|
|:-|:-|
|:tada:|Initalisation|
|:sparkles:|New feature|
|:books:|Documentation|
|:art:|Styling|
|:hammer:|Refactoring|
|:construction:|Work in progress|
|:wrench:|Configuration|
|:ok_hand:|Validation (Merge)|
|:on:|Api|
|:bug:|Bugfix|
|:lock:|Security|