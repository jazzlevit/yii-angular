<?php
/**
 * Created by PhpStorm.
 * User: Aleksey Stetsenko
 * Date: 12/14/2015
 * Time: 2:47 PM
 */

namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use frontend\models\ContactForm;
use yii\rest\Controller;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use yii\filters\AccessControl;
use frontend\models\SignupForm;

class ApiController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'only' => ['dashboard'],
        ];
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::className(),
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'only' => ['dashboard'],
            'rules' => [
                [
                    'actions' => ['dashboard'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];
        return $behaviors;
    }

    public function actionLogin()
    {
        $model = new LoginForm();

        if ($model->load(Yii::$app->getRequest()->getBodyParams(), '') && $model->login()) {
            return ['access_token' => Yii::$app->user->identity->getAuthKey()];
        } else {
            $model->validate();
            return $model;
        }
    }

    public function actionDashboard()
    {
        $response = [
            'username' => Yii::$app->user->identity->username,
            'access_token' => Yii::$app->user->identity->getAuthKey(),
        ];

        return $response;
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->getRequest()->getBodyParams(), '') && $model->validate()) {
            if (true /*$model->sendEmail(Yii::$app->params['adminEmail'])*/) {
                $response = [
                    'flash' => [
                        'class' => 'success',
                        'message' => 'Thank you for contacting us. We will respond to you as soon as possible.',
                    ]
                ];
            } else {
                $response = [
                    'flash' => [
                        'class' => 'error',
                        'message' => 'There was an error sending email.',
                    ]
                ];
            }
            return $response;
        } else {
            $model->validate();
            return $model;
        }
    }

    public function actionSignUp()
    {
        $model = new SignupForm();

        if ($model->load(Yii::$app->getRequest()->getBodyParams(), '') && $model->validate()) {
            $user = $model->signup();
            if ($user) {
                Yii::$app->user->login($user, 3600 * 24 * 30);

                $response = [
                    'access_token' => Yii::$app->user->identity->getAuthKey(),
                    'flash' => [
                        'class' => 'success',
                        'message' => 'Thank you. You are successfully sign up.',
                    ]
                ];
            } else {
                $response = ['flash' => [
                    'class' => 'error',
                    'message' => 'Something wrong.',
                ]];
            }
            return $response;
        } else {
            $model->validate();
            return $model;
        }
    }
}