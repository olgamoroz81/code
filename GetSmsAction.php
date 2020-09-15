<?php

use application\components;
use application\models;
use application\models\Defines;

Yii::import('API.models.SMS');

/**
 * @author olga.moroz@soxes.ch
 */
class GetSmsAction extends CMSAjaxAction
{
    public function doRun()
    {
        $result = [];
        $messages = [];

        $alarmId = (int)Yii::app()->request->getParam('alarmId', 0);
        $email = Yii::app()->request->getParam('email', '');
        $canStop = Yii::app()->request->getParam('canstop', 0);

        if (empty($alarmId) || empty($email)) {
            throw new Exception(Yii::t('cms', ''));
        }

        $smsData = \AlarmSms::model()->findAll('alarmId = :alarmId AND email = :email AND status = :status', [
            ':alarmId' => $alarmId,
            ':email' => $email,
            ':status' => Defines\Alarm\Sms\Status::DONE,
        ]);

        $helper = new components\SoxesTwilio\Helper;

        foreach ($smsData as $data) {
            $sms = new models\Alarm\Sms($data);
            $messages[] = $helper->getMessage($sms, $canStop);
        }

        $result['widget'] = $this->controller->renderPartial(
            $this->controller->module->getThemePath('partials.sms-details', 'alarmnotification'),
            array(
                'messages' => $messages,
            ),
            true,
            true
        );

        return $result;
    }
}
