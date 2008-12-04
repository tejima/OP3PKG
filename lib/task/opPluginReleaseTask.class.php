<?php

class opPluginReleaseTask extends sfBaseTask
{
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('name', sfCommandArgument::REQUIRED, 'The plugin name'),
    ));

    $this->namespace        = 'opPlugin';
    $this->name             = 'release';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [opPlugin:release|INFO] task does things.
Call it with:

  [./symfony opPlugin:release name|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    $name = $arguments['name'];

    while (
      !($version = $this->ask('Type plugin version'))
      || !preg_match('/^[.\d]+(alpha|beta|rc)?[.\d]*$/i', $version)
    )
    {
      $this->logBlock('invalid format.', 'ERROR');
    }

    while (
      !($stability = $this->ask('Choose stability (stable, alpha, beta or devel)'))
      || !in_array($stability, array('stable', 'alpha', 'beta', 'devel'))
    )
    {
      $this->logBlock('invalid format.', 'ERROR');
    }

    while (
      !($note = $this->ask('Type release note'))
    );

    $this->logBlock('These informations are inputed:', 'COMMENT');
    $this->log($this->formatList(array(
      'The Plugin Name     ' => $name,
      'The Plugin Version  ' => $version,
      'The Plugin Stability' => $stability,
      'The Release note    ' => $note,
    )));

    if ($this->askConfirmation('Is it OK to start this task? (y/n)'))
    {
      $this->doRelease($name, $version, $stability, $note);
      $this->clearCache();
    }
  }

  protected function doRelease($name, $version, $stability, $note)
  {
    $defineTask = new opPluginDefineTask($this->dispatcher, $this->formatter);
    $defineTask->run(array('name' => $name, 'version' => $version, 'stability' => $stability, 'note' => '"'.$note.'"'));
    $archiveTask = new opPluginArchiveTask($this->dispatcher, $this->formatter);
    $archiveTask->run(array('name' => $name));
    $uploadTask = new opPluginUploadTask($this->dispatcher, $this->formatter);
    $uploadTask->run(array('name' => $name));
  }

  protected function clearCache()
  {
    $task = new sfCacheClearTask($this->dispatcher, $this->formatter);
    $task->run();
  }

  protected function formatList($list)
  {
    $result = '';

    foreach ($list as $key => $value)
    {
      $result .= $this->formatter->format($key, 'INFO')."\t";
      $result .= $value."\n";
    }

    return $result;
  }
}
