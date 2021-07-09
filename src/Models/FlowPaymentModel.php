<?php

namespace Themey99\LaravelFlowPayments\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Themey99\LaravelFlowPayments\Contracts\FlowPaymentModelContract;

class FlowPaymentModel extends Model implements FlowPaymentModelContract
{
    const STATUS_INIT = 0;
    const STATUS_PENDING = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_REJECTED = 3;
    const STATUS_ANULED = 4;

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable()
    {
        return config('flow.table_name', parent::getTable());
    }

    protected $casts = [
        'modelable_id' => 'int',
        'optional' => 'array',
        'pending_info' => 'array',
        'payment_data' => 'array',
    ];

    protected $dates = [
        'request_date',
    ];

    protected $fillable = [
        'modelable_id',
        'modelable_type',
        'flow_order',
        'commerce_order',
        'request_date',
        'status',
        'subject',
        'currency',
        'amount',
        'payer',
        'optional',
        'pending_info',
        'payment_data',
        'url_confirmation',
        'url_return',
        'url_redirect',
    ];

    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        parent::creating(function ($model) {
            if (!$model->modelable_id) {
                /**
                 * @var User
                 */
                $user = Auth::user();

                $model->modelable_type = $user->getMorphClass();
                $model->modelable_id = $user->id;
            }
        });
    }

    public function modelable()
    {
        return $this->morphTo();
    }

    #Methods

    public function findByCommerceOrder(string $commerceOrder, $thowException = true)
    {
        return $thowException
            ? $this->where('commerce_order', $commerceOrder)->firstOrFail()
            : $this->where('commerce_order', $commerceOrder)->first();
    }

    public function createFromOrder(Collection $order): FlowPaymentModelContract
    {
        return $this->create([
            'flow_order' => $order['flowOrder'],
            'commerce_order' => $order['commerceOrder'],
            'status' => $this::STATUS_INIT,
            'amount' => $order['amount'],
            'payer' => $order['email'],
            'url_confirmation' => $order['urlConfirmation'],
            'url_return' => $order['urlReturn'],
            'url_redirect' => $order['urlRedirect'],
            'optional' => $order['optional'],
        ]);
    }

    public function updateFromConfirmation(array $order): FlowPaymentModelContract
    {
        $flowPayment = $this->findByCommerceOrder($order['commerceOrder']);

        if ($flowPayment->status != $this::STATUS_COMPLETED) {
            if (isset($order['paymentData'])) {
                if ($flowPayment->payment_data) {
                    $paymentData = collect($flowPayment->payment_data);
                    $flowPayment->payment_data = $paymentData->add(
                        $order['paymentData']
                    );
                } else {
                    $flowPayment->payment_data = [
                        $order['paymentData']
                    ];
                }
            }

            if (isset($order['pending_info'])) {
                if ($flowPayment->pending_info) {
                    $pendingInfo = collect($flowPayment->pending_info);
                    $flowPayment->pending_info = $pendingInfo->add(
                        $order['pending_info']
                    );
                } else {
                    $flowPayment->pending_info = [
                        $order['pending_info']
                    ];
                }
            }
        }

        $flowPayment->fill([
            'request_date' => $order['requestDate'],
            'subject' => $order['subject'],
            'status' => $order['status'],
        ]);

        if ($flowPayment->isDirty()) {
            $flowPayment->save();
        }

        return $flowPayment;
    }
}
