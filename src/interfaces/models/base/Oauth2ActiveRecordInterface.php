<?php

namespace rhertogh\Yii2Oauth2Server\interfaces\models\base;

use yii\db\ActiveRecordInterface;

interface Oauth2ActiveRecordInterface extends ActiveRecordInterface
{
    /**
     * Declares the name of the database table associated with this AR class.
     * By default this method returns the class name as the table name by calling [[Inflector::camel2id()]]
     * with prefix [[Connection::tablePrefix]]. For example if [[Connection::tablePrefix]] is `tbl_`,
     * `Customer` becomes `tbl_customer`, and `OrderItem` becomes `tbl_order_item`. You may override this method
     * if the table is not named after this convention.
     * @return string the table name
     * @since 1.0.0
     */
    public static function tableName();

    /**
     * Find a model by $condition, if no records is found a new model will be instantiated with the $condition set
     * as attributes.
     * @param array $condition Similar to [[findOne()]] (primary key value or a set of column values)
     * @return static
     * @see findOne()
     * @since 1.0.0
     */
    public static function findOrCreate($condition);

    /**
     * Saves the current record, similar to [[save()]] but throws an exception on validation failure.
     * @param bool $runValidation whether to perform validation (calling [[validate()]])
     * before saving the record. Defaults to `true`. If the validation fails, the record
     * will not be saved to the database and this method will return `false`.
     * @param array $attributeNames list of attribute names that need to be saved. Defaults to null,
     * meaning all attributes that are loaded from DB will be saved.
     * @return $this
     * @see save()
     * @since 1.0.0
     */
    public function persist($runValidation = true, $attributeNames = null);

    /**
     * Returns the errors for all attributes as a one-dimensional array.
     * @param bool $showAllErrors boolean, if set to true every error message for each attribute will be shown otherwise
     * only the first error message for each attribute will be shown.
     * @return array errors for all attributes as a one-dimensional array. Empty array is returned if no error.
     * @since 1.0.0
     */
    public function getErrorSummary($showAllErrors);
}
