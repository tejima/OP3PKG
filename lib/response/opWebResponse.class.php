<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 *
 * @package    OpenPNE
 * @subpackage response
 * @author     Kousuke Ebihara <ebihara@php.net>
 */
class opWebResponse extends sfWebResponse
{
  public function getTitle()
  {
    $result = parent::getTitle();
    if (!$result)
    {
      $result = opConfig::get('sns_title') ? opConfig::get('sns_title') : opConfig::get('sns_name');
    }

    return $result;
  }
}
