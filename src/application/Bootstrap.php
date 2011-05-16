<?php
/**
 * Copyright (c) 2011 Andreas Heigl<andreas@heigl.org
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a 
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, 
 * and/or sell copies of the Software, and to permit persons to whom the 
 * Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL 
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING 
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER 
 * DEALINGS IN THE SOFTWARE.
 *
 * @category  Gallery
 * @package   Application
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright 2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license MIT-License
 * @version   GIT: $Revision: $
 * @since     15.05.2011
 */
 
/**
 * The main Bootstrap-Class for the Zend-Frmaework
 *
 * @category  Gallery
 * @package   Application
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright 2011 Andreas Heigl<andreas@heigl.org>
 * @license   http://www.opensource.org/licenses/mit-license MIT-License
 * @version   GIT: $Revision: $
 * @since     15.05.2011
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    /**
     * Initialize the Configuration opf the gallery
     *
     * @return void
     */
    protected function _initAppConfig()
    {
        $config = new Zend_Config_Ini(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'configs' . DIRECTORY_SEPARATOR . 'gallery.ini', APPLICATION_ENV);
        Zend_Registry::set('gallery_config', $config);
    }
    
    /**
     * Initialize translation-handling
     *
     * @return void
     */
    protected function _initTranslation()
    {
        $translate = new Zend_Translate(
                         'xliff',
                         APPLICATION_PATH . DIRECTORY_SEPARATOR . 'locale',
                         null,
                         array('scan' => Zend_Translate::LOCALE_DIRECTORY));

        // Eine Log Instanz erstellen
        $writer = new Zend_Log_Writer_Stream(
             realpath(APPLICATION_PATH .
             DIRECTORY_SEPARATOR .
            '..' .
             DIRECTORY_SEPARATOR .
             'log' ).
            DIRECTORY_SEPARATOR .
             date ( 'Ymd' ) . '-TranslateError.log' );
        $log = new Zend_Log($writer);
        //
        // Diese der Übersetzungs-Instanz hinzufügen
        $translate->setOptions(array(
             'log' => $log,
                  'logUntranslated' => true));
 
        Zend_Registry::set('Zend_Translate', $translate);
    }
}

