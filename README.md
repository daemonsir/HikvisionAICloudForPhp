## HikvisionAICloudForPhp

因为[海康开放平台](https://open.hikvision.com/)很多接口暂时未提供PHP版本的SKD，所以我个人开发了**HikvisionAICloudForPhp**，这个是人脸识别和门禁系统的PHP版本，项目中的HTTP工具类也适用其他接口，自行修改即可。

## composer.json
```json
{
    "require": {
        "alibabacloud/client": "1.5",
        "hikvisionapi/face": "@dev"
    }
}
```

## 目录结构

```php
/
├── src                         源代码根目录
│   ├── Dictionary.php          接口message映射类
│   ├── HikDoorApi.php          海康门禁设备接口调用类
│   ├── HikFaceApi.php          海康人脸识别接口调用类
│   └── HttpUtillib.php         海康平台验签Http工具类，此工具调用其他API也适用
```
## License
MIT
