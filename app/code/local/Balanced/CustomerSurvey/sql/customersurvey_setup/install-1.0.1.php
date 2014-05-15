<?php

$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE IF NOT EXISTS {$this->getTable('customersurvey/survey')} (
  `customersurvey_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `enabled` tinyint(4) NOT NULL,
  `code` text NOT NULL,
  `code_title` text NOT NULL,
  PRIMARY KEY (`customersurvey_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

CREATE TABLE IF NOT EXISTS {$this->getTable('customersurvey/questions')} (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `customersurvey_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `answer_type` text NOT NULL,
  `sort_order` int(11) NOT NULL,
  `possible_answers` text,
  PRIMARY KEY (`question_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=141 ;

CREATE TABLE IF NOT EXISTS {$this->getTable('customersurvey/results')} (
  `result_id` int(11) NOT NULL AUTO_INCREMENT,
  `customersurvey_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer` text NOT NULL,
  `customer_id` int(11) NOT NULL,
  `input_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `unique_id` int(11) NOT NULL,
  PRIMARY KEY (`result_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=174 ;
");

$installer->endSetup();
