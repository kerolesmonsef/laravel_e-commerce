<?php

namespace App\Http\Controllers;

use Gloudemans\Shoppingcart\Facades\Cart;
use Gloudemans\Shoppingcart\Cart as Cart2;
use Illuminate\Http\Request;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;

/**
 * @property ApiContext appContext
 * @property mixed client_secret
 * @property mixed client_id
 */
class CheckoutController extends Controller
{
    public function __construct()
    {
        $mode = config('paypal.mode', 'sandbox');
        $credentials = config("paypal.$mode");
        $this->client_id = $credentials['client_id'];
        $this->client_secret = $credentials['client_secret'];

        $this->appContext = new ApiContext(new OAuthTokenCredential($this->client_id, $this->client_secret));
        $this->appContext->setConfig(config('paypal.settings'));

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (Cart::count() <= 0) {
            return redirect()->back()->withErrors('No Items In The Cart');
        }
        $total_price = Cart::total(null, null, '');

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $itemList = new ItemList();

        foreach (Cart::content() as $cart_item) {
            $item = new Item();
            $item->setName($cart_item->model->name)
                ->setCurrency("USD")
                ->setQuantity($cart_item->qty)
                ->setSku($cart_item->model->slug)
                ->setPrice($cart_item->model->price);

            $itemList->addItem($item);
        }

        $details = new Details();
        $details->setTax(Cart::tax())
            ->setSubtotal(Cart::subtotal());

        $amount = new Amount();
        $amount
            ->setCurrency("USD")
            ->setTotal($total_price)
            ->setDetails($details);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription("Payment Paypal")
            ->setInvoiceNumber(uniqid("", TRUE));

        $redirectUrl = new RedirectUrls();
        $redirectUrl
            ->setReturnUrl(route('checkout.redirect.success'))
            ->setCancelUrl(route('checkout.redirect.fail'));

        $payment = new Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrl)
            ->setTransactions([$transaction]);

        try {
            $payment->create($this->appContext);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
        $payment_link = $payment->getApprovalLink();

        return redirect($payment_link);

    }

    public function success()
    {
        if (!request('PayerID') || !request('paymentId') || !request('token')) {
            dd("Payment Error");
        }
        Cart::instance("default")->destroy();
        $payment = Payment::get(request('paymentId'), $this->appContext);
        $execution = new PaymentExecution();
        $execution->setPayerId(request('PayerID'));

        try {
            $result = $payment->execute($execution, $this->appContext);
        } catch (PayPalConnectionException  $e) {
            dump($e->getData(), ($e->getCode()));
            dd('');
        }


        if ($result->getState() == 'approved') {
            return view("thank-you");
        }
        dump("error");
        dump($result);
    }

    public function fail()
    {
        return redirect()->route('cart.index')->withErrors("Payment Error");
    }

}
