<?php

// This class was automatically generated by a giiant build task.
// You should not change it manually as it will be overwritten on next build.

namespace rhertogh\Yii2Oauth2Server\models\base;

use Yii;

/**
 * This is the base-model class for table "oauth2_auth_code".
 *
 * @property string $id
 * @property string $identifier
 * @property string $redirect_uri
 * @property string $expiry_date_time
 * @property integer $client_id
 * @property integer $user_id
 * @property integer $enabled
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property \rhertogh\Yii2Oauth2Server\models\Oauth2AuthCodeScope[] $authCodeScopes
 * @property \rhertogh\Yii2Oauth2Server\models\Oauth2Client $client
 * @property \rhertogh\Yii2Oauth2Server\models\Oauth2Scope[] $scopes
 * @property string $aliasModel
 *
 * phpcs:disable Generic.Files.LineLength.TooLong
 */
abstract class Oauth2AuthCode extends \rhertogh\Yii2Oauth2Server\models\base\Oauth2BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['identifier', 'expiry_date_time', 'client_id', 'user_id', 'created_at', 'updated_at'], 'required'],
            [['expiry_date_time'], 'safe'],
            [['client_id', 'user_id', 'enabled', 'created_at', 'updated_at'], 'integer'],
            [['identifier', 'redirect_uri'], 'string', 'max' => 255],
            [['identifier'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('oauth2', 'ID'),
            'identifier' => Yii::t('oauth2', 'Identifier'),
            'redirect_uri' => Yii::t('oauth2', 'Redirect Uri'),
            'expiry_date_time' => Yii::t('oauth2', 'Expiry Date Time'),
            'client_id' => Yii::t('oauth2', 'Client ID'),
            'user_id' => Yii::t('oauth2', 'User ID'),
            'enabled' => Yii::t('oauth2', 'Enabled'),
            'created_at' => Yii::t('oauth2', 'Created At'),
            'updated_at' => Yii::t('oauth2', 'Updated At'),
        ];
    }

    /**
     * @return \rhertogh\Yii2Oauth2Server\interfaces\models\queries\Oauth2AuthCodeScopeQueryInterface|\yii\db\ActiveQuery
     */
    public function getAuthCodeScopes()
    {
        return $this->hasMany(\rhertogh\Yii2Oauth2Server\models\Oauth2AuthCodeScope::className(), ['auth_code_id' => 'id'])->inverseOf('authCode');
    }

    /**
     * @return \rhertogh\Yii2Oauth2Server\interfaces\models\queries\Oauth2ClientQueryInterface|\yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(\rhertogh\Yii2Oauth2Server\models\Oauth2Client::className(), ['id' => 'client_id'])->inverseOf('authCodes');
    }

    /**
     * @return \rhertogh\Yii2Oauth2Server\interfaces\models\queries\Oauth2ScopeQueryInterface|\yii\db\ActiveQuery
     */
    public function getScopes()
    {
        return $this->hasMany(\rhertogh\Yii2Oauth2Server\models\Oauth2Scope::className(), ['id' => 'scope_id'])->via('authCodeScopes');
    }



    /**
     * @inheritdoc
     * @return \rhertogh\Yii2Oauth2Server\interfaces\models\queries\Oauth2AuthCodeQueryInterface|\yii\db\ActiveQuery the active query used by this AR class.
     */
    public static function find()
    {
        return Yii::createObject(\rhertogh\Yii2Oauth2Server\interfaces\models\queries\Oauth2AuthCodeQueryInterface::class, [get_called_class()]);
    }
}
