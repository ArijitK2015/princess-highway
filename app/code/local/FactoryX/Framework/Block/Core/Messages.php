<?php

/**
 * Class FactoryX_Framework_Block_Core_Messages
 */
class FactoryX_Framework_Block_Core_Messages extends Mage_Core_Block_Messages
{
    /**
     * Retrieve messages in HTML format grouped by type
     * @return string
     * @internal param string $type
     */
    public function getGroupedHtml()
    {
        $types = array(
            Mage_Core_Model_Message::ERROR  =>  array("bg-danger","exclamation-triangle"),
            Mage_Core_Model_Message::WARNING    =>  array("bg-warning","exclamation-circle"),
            Mage_Core_Model_Message::NOTICE     =>  array("bg-info","info-circle"),
            Mage_Core_Model_Message::SUCCESS    => array("bg-success","check-circle")
        );
        $html = '';
        foreach ($types as $type    =>  $design) {
            if ( $messages = $this->getMessages($type) ) {
                if ( !$html ) {
                    $html .= '<' . $this->_messagesFirstLevelTagName . ' class="messages list-unstyled">';
                }
                $html .= '<' . $this->_messagesSecondLevelTagName . ' class="' . $design[0] . ' ' . $type . '-msg">';
                $html .= '<i class="fa fa-lg fa-' . $design[1] . ' pull-left "></i>';
                $html .= '<' . $this->_messagesFirstLevelTagName . ' class="list-unstyled">';

                foreach ( $messages as $message ) {
                    $html.= '<' . $this->_messagesSecondLevelTagName . '>';
                    $html.= '<' . $this->_messagesContentWrapperTagName . '>';
                    $html.= ($this->_escapeMessageFlag) ? $this->escapeHtml($message->getText()) : $message->getText();
                    $html.= '</' . $this->_messagesContentWrapperTagName . '>';
                    $html.= '</' . $this->_messagesSecondLevelTagName . '>';
                }
                $html .= '</' . $this->_messagesFirstLevelTagName . '>';
                $html .= '</' . $this->_messagesSecondLevelTagName . '>';
            }
        }
        if ( $html) {
            $html .= '</' . $this->_messagesFirstLevelTagName . '>';
        }
        return $html;
    }
}