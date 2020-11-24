<?php

namespace Compact\BossBar;

class BossHealth{

  public function getMinValue(){
    return 0;
  }
  public function getMaxValue(){
    return 10;
  }
  public function getValue(){
    return 10;
  }
  public function getName(){
    return "minecraft:health";
  }
  public function getDefaultValue(){
    return 10;
  }
}