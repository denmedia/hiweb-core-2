<?php

/**
 * Created by PhpStorm.
 * User: hiweb
 * Date: 30.06.2016
 * Time: 15:38
 */
class hiweb_path
{


    /**
     * Возвращает текущий адрес URL
     * @version 1.0.2
     */
    public function getStr_urlFull($trimSlashes = true)
    {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
        return rtrim('http' . ($https ? 's' : '') . '://' . $_SERVER['HTTP_HOST'], '/') . ($trimSlashes ? rtrim($_SERVER['REQUEST_URI'], '/\\') : $_SERVER['REQUEST_URI']);
    }


    /**
     * Возвращает корневой URL
     * @return string
     *
     * @version 1.3
     */
    public function getStr_baseUrl()
    {
        //if(hiweb()->cacheExists()) return hiweb()->cache();
        $root = ltrim($this->getStr_baseDir(), '/');
        $query = ltrim(str_replace('\\', '/', dirname($_SERVER['PHP_SELF'])), '/');
        $rootArr = array();
        $queryArr = array();
        foreach (array_reverse(explode('/', $root)) as $dir) {
            $rootArr[] = rtrim($dir . '/' . end($rootArr), '/');
        }
        foreach (explode('/', $query) as $dir) {
            $queryArr[] = ltrim(end($queryArr) . '/' . $dir, '/');
        }
        $rootArr = array_reverse($rootArr);
        $queryArr = array_reverse($queryArr);
        $r = '';
        foreach ($queryArr as $dir) {
            foreach ($rootArr as $rootDir) {
                if ($dir == $rootDir) {
                    $r = $dir;
                    break 2;
                }
            }
        }
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
        return rtrim('http' . ($https ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . '/' . $r, '/');
    }

    /**
     * Возвращает корневую папку сайта. Данная функция автоматически определяет корневую папку сайта, отталкиваясь на поиске папок с файлом index.php
     *
     * @return string
     *
     * @version 1.4
     */
    public function getStr_baseDir()
    {
        $full_path = getcwd();
        $ar = explode("wp-", $full_path);
        return rtrim($ar[0], '\\/');
    }

    /**
     * Возвращает URL с измененным QUERY фрагмнтом
     * @param null $url
     * @param array $addData
     * @param array $removeKeys
     * @return string
     *
     * @version 1.4
     */
    public function getStr_urlQuery($url = null, $addData = array(), $removeKeys = array())
    {
        if (is_null($url) || trim($url) == '') $url = $this->getStr_urlFull();
        $url = explode('?', $url);
        $urlPath = array_shift($url);
        $query = implode('?', $url);
        ///
        $params = explode('&', $query);
        $paramsPair = array();
        foreach ($params as $param) {
            if (trim($param) == '') continue;
            list($key, $val) = explode('=', $param);
            $paramsPair[$key] = $val;
        }
        ///Add
        if (is_array($addData)) foreach ($addData as $key => $value) {
            $paramsPair[$key] = $value;
        } elseif (is_string($addData) && trim($addData) != '') {
            $paramsPair[] = $addData;
        }
        ///Remove
        if (is_array($removeKeys)) foreach ($removeKeys as $key => $value) {
            if (is_string($key) && isset($paramsPair[$key])) unset($paramsPair[$key]);
            elseif (isset($paramsPair[$value])) unset($paramsPair[$value]);
        } else if (is_string($removeKeys) && trim($removeKeys) != '' && isset($paramsPair[$removeKeys])) unset($paramsPair[$removeKeys]);
        ///
        $params = array();
        foreach ($paramsPair as $key => $value) {
            $params[] = (is_string($key) ? $key . '=' : '') . htmlentities($value, ENT_QUOTES, 'UTF-8');
        }
        ///
        return count($paramsPair) > 0 ? $urlPath . '?' . implode('&', $params) : $urlPath;
    }

    /**
     * Возвращает расширение файла, уть которого указан в аргументе $path
     * @param $path
     * @return string
     */
    public function getStr_fileExtension($path)
    {
        $pathInfo = pathinfo($path);
        return isset($pathInfo['extension']) ? $pathInfo['extension'] : '';
    }


    /**
     * Конвертирует путь до файла в URL до файла
     * @param $path
     * @return mixed
     */
    public function getStr_urlFromPath($path)
    {
        $path = str_replace('\\', '/', realpath($path));
        return str_replace($this->getStr_baseDir(), $this->getStr_baseUrl(), $path);
    }


    /**
     * @param null $url
     * @return array
     */
    public function getArr_fromUrl($url = null)
    {
        if (is_null($url) || trim($url) == '') $url = $this->getStr_urlFull();
        $urlExplode = explode('?', $url);
        $urlPath = array_shift($urlExplode);
        $query = implode('?', $urlExplode);
        ///params
        $paramsPair = array();
        if (trim($query) != '') {
            $params = explode('&', $query);
            foreach ($params as $param) {
                list($key, $val) = explode('=', $param);
                $paramsPair[$key] = $val;
            }
        }
        ///
        $baseUrl = $this->getStr_baseUrl();
        $baseUrl = strpos($url, $baseUrl) === 0 ? $baseUrl : false;
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443;
        $shema = $https ? 'https://' : 'http://';
        $dirs = array();
        $domain = '';
        if ($baseUrl != false) {
            $dirs = explode('/', trim(str_replace($shema, '', $urlPath), '/'));
            $domain = array_shift($dirs);
        }
        return array(
            'url' => $url,
            'base_url' => $baseUrl,
            'shema' => $shema,
            'domain' => $domain,
            'dirs' => implode('/', $dirs),
            'dirs_arr' => $dirs,
            'params' => $query,
            'params_arr' => $paramsPair
        );
    }

    /**
     * Возвращает папки или папку(если указать индекс) из URL
     * @param null $url
     * @param int $index
     * @return bool|array|string
     */
    public function getArr_dirsFromUrl($url = null, $index = null)
    {
        $urlArr = $this->getArr_fromUrl($url);
        return is_int($index) ? (isset($urlArr['dirs_arr'][$index]) ? $urlArr['dirs_arr'][$index] : false) : $urlArr['dirs_arr'];
    }


    /**
     * Возвращает TRUE, если текущая страница являеться домашней
     * @return bool
     */
    public function is_home()
    {
        return $this->getStr_baseUrl() == $this->getStr_urlFull();
    }


    /**
     * Возвращает TRUE, если текущая страница соответствует указанному SLUG
     * @param string $pageSlug
     * @return bool
     */
    public function is($pageSlug = '')
    {
        $currentUrl = ltrim(str_replace($this->getStr_baseUrl(), '', $this->getStr_urlFull()), '/\\');
        $pageSlug = ltrim($pageSlug, '/\\');
        return (strpos($currentUrl, $pageSlug) === 0);
    }


}