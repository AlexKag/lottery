<?php

namespace common\components\lottery\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\components\lottery\models\L6x45;

/**
 * L6x45Search represents the model behind the search form about `common\components\lottery\models\L6x45`.
 */
class L6x45Search extends L6x45
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'tickets', 'bets', 'created_at', 'updated_at'], 'integer'],
            ['enabled', 'boolean'],
            [['draw', 'wins_stat'], 'safe'],
            [['superprize', 'superprize_gain', 'pool', 'paid_out'], 'number'],
            ['draw_at', 'default', 'value' => null],
            ['draw_at', 'date', 'timestampAttribute' => 'draw_at', 'format' => 'php:d-m-Y'],
            ['draw_at', 'filter', 'filter' => function($value) {
                    return static::_ceilToDraw($value);
                }],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = L6x45::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'draw_at' => $this->draw_at,
            'superprize' => $this->superprize,
            'superprize_gain' => $this->superprize_gain,
            'tickets' => $this->tickets,
            'bets' => $this->bets,
            'pool' => $this->pool,
            'paid_out' => $this->paid_out,
            'enabled' => $this->enabled,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'draw', $this->draw])
                ->andFilterWhere(['like', 'wins_stat', $this->wins_stat]);

        return $dataProvider;
    }

}
