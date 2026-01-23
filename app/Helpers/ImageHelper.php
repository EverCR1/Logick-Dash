<?php

namespace App\Helpers;

class ImageHelper
{
    /**
     * Obtener URL de imagen óptima para mostrar
     */
    public static function getImageUrl($image, $size = 'medium')
    {
        // Si no hay imagen, retornar placeholder
        if (!$image) {
            return self::getPlaceholder();
        }

        // Si es un array (viene de la API)
        if (is_array($image)) {
            // Si tiene URL miniatura (de ImgBB)
            if ($size === 'thumb' && isset($image['url_thumb'])) {
                return $image['url_thumb'];
            }
            
            if ($size === 'medium' && isset($image['url_medium'])) {
                return $image['url_medium'];
            }
            
            // URL original por defecto
            return $image['url'] ?? self::getPlaceholder();
        }

        // Si es string (URL directa)
        if (is_string($image)) {
            return $image;
        }

        return self::getPlaceholder();
    }

    /**
     * Placeholder para imágenes faltantes
     */
    public static function getPlaceholder($width = 300, $height = 300)
    {
        return "https://via.placeholder.com/{$width}x{$height}?text=Sin+Imagen";
    }

    /**
     * Verificar si una URL es de ImgBB
     */
    public static function isImgBBUrl($url)
    {
        return strpos($url, 'i.ibb.co') !== false || 
               strpos($url, 'ibb.co') !== false;
    }

    /**
     * Obtener dominio de la API para imágenes
     */
    public static function getApiUrl()
    {
        return rtrim(config('api.url', 'http://localhost:8000'), '/');
    }

    /**
     * Convertir URL relativa a absoluta
     */
    public static function makeAbsoluteUrl($url)
    {
        // Si ya es una URL completa
        if (strpos($url, 'http') === 0) {
            return $url;
        }

        // Si es una ruta de storage
        if (strpos($url, '/storage/') === 0) {
            return self::getApiUrl() . $url;
        }

        // Por defecto, asumir que es relativa a la API
        return self::getApiUrl() . '/' . ltrim($url, '/');
    }
}