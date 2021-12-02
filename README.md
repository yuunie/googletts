# GoogleTTS 获取 Google 翻译 TTS 链接 （文字转语音）
一个基于 GoogleTTS 的文字转语音 PHP 类

## 开始

已经提交到Packagist，你可以使用Composer安装
```
composer require yuunie/googletts
```

## 基本信息

* 命名空间 namespace
```
Yuunie
```
* 类名 class
```
GoogleTTS
```
* 方法 function
```
static getUrl("需要TTS的文字", [播放速度0-1], [播放语言,默认中文])
```

## 使用
```
Yuunie/GoogleTTS::getUrl("你好")
```

## 细节
获取到url后尽量先保存为mp3文件,直接使用url可能会出现一些问题

> 获取tk的方法来自百度(吾爱破解)
