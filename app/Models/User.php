<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Auth;
use App\Models\user_has_refs;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Json;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path', // user uploaded photo
        'profile_photo_url', // default photo
        'coin',
        'reference',
        'reference_accepted_at', // backend_logic
        'active_nav', // backend_logic
        'gender',

        'vip',
        'phone',
        'country',
        'country_code',
        'zip',
        'state',
        'city',
        'line1',
        'line2',

        'currency',
        'currency_sing',
        'language',
        'site_language',
        'kyc_status', // backend_logic
        'is_active', // backend_logic 
        'metadata', // backend_logic

        'dob', // date of birth
        'bio', // about 
    ];



    public $editAble = [
        'name',
        'email',
        'phone',
        'zip',
        'line1',
        'line2',

        'dob', // date of birth
        'bio', // about 
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'deleted_at',
        'email_verified_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'metadata' => 'array',
        ];
    }

    //  protected function name(): Attribute
    // {
    //     return Attribute::make(
    //         set: fn ($value) => ucwords(strtolower($value)), // Capitalizes each word
    //     );
    // }


    protected function state(): Attribute
    {
        return Attribute::make(
            set: fn($value) => ucfirst(strtolower($value)), // Capitalizes each word
        );
    }

    protected function city(): Attribute
    {
        return Attribute::make(
            set: fn($value) => ucfirst(strtolower($value)), // Capitalizes each word
        );
    }

    protected function country(): Attribute
    {
        return Attribute::make(
            set: fn($value) => ucfirst($value), // uppercase the world
        );
    }

    /**
     * give user default 'user' role 
     * when model is created
     */
    protected static function boot(): void
    {
        parent::boot();

        static::created(function (User $user) {
            // give an default role
            $user->syncRoles('user');
            $user->coin = 0;
            $user->profile_photo_url = 'https://source.unsplash.com/random';

            // permission to access user pane.
            $user->syncPermissions('access_users_dashboard');

            /**
             * create a reff code, if comission is turn on to config
             */

            if (config('app.comission')) {
                $length = strlen($user->id);

                if ($length >= 4) {
                    $ref = $user->id;
                } else {
                    $ref = str_pad($user->id, 4, '0', STR_PAD_LEFT);
                }
                user_has_refs::create(
                    [
                        'user_id' => $user->id,
                        'ref' => date('ym') . $ref,
                        'status' => 1,
                    ]
                );
            }

            $user->save();


            // created user_has_address table belongs to user
            User_has_address::create(
                [
                    'user_id' => $user->id,
                ]
            );
        });
    }

    /**
     * scope method 
     */
    public function scopeWithAdmin($query)
    {
        return $query->where('email', '=', config('app.system_email'));
    }

    public function scopeWithoutAdmin($query)
    {
        return $query->where('email', '!=', config('app.system_email'));
    }

    public function scopeActive($query)
    {
        return $query->whereNotNull('is_active');
    }

    public function scopeEVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    /**
     * Determined the user hold the specific permissions
     */
    public function ableTo($permission)
    {
        return $this->permisions()->contains($permission);
    }

    protected function permisions()
    {
        return $this->getPermissionNames();
    }


    public function abailCoin()
    {
        // return the available coin after substruct 500
        // 500 is the minimum balance to hold for user
        $active_nav = $this->active_nav;
        if ($active_nav == 'reseller') {
            return $this->coin - $this->resellerShop()?->fixed_amount;
        } elseif ($active_nav == 'vendor') {
            return $this->coin - $this->vendorShop()?->fixed_amount;
        } elseif ($active_nav == 'rider') {
            return $this->coin - $this->isRider()?->fixed_amount;
        } else {
            return $this->coin - 500;
        }
    } 

    /**
     * @return Array
     */
    public function haveEnoughBalance($balance): array
    {
        $data = array();
        $data['status'] = false;
        $data['message'] = "Too Low Balance";
        $data['mb'] = $this->abailCoin(); // get the usable balance after substruct of 500
        $data['cb'] = $this->abailCoin() - $balance; // balance after substruct

        if ($this->abailCoin() > $balance && $data['cb'] > 0) {
            $data['status'] = true;
            $data['message'] = 'Continue Process';
        }

        return $data;
    }


    //////////////// 
    // Relations //
    ///////////////

    /**
     * user address
     * 
     * @return User_has_address
     */
    public function address()
    {
        return $this->hasMany(User_has_address::class);
    }


    /**
     * @return reff_code
     */
    public function myRef()
    {
        return $this->hasOne(user_has_refs::class)->withDefault([
            'ref' => null,
        ]);
    }

    /**
     * @return reffered_user
     */
    public function getReffOwner()
    {
        return $this->belongsTo(user_has_refs::class, 'reference', 'ref')->withDefault([
            'ref' => null,
        ]);
    }

    /**
     * get vip reff
     */
    public function getMyvipRef()
    {
        return $this->hasMany(vip::class, 'refer', 'id');
    }

    /**
     * @return the referred user
     */
    public function referred()
    {
        return User::where(['reference' => $this->referene]);
    }


    public function requestsToBeVendor()
    {
        return $this->hasMany(vendor::class);
    }
    public function requestsToBeReseller()
    {
        return $this->hasMany(reseller::class);
    }
    public function requestsToBeRider()
    {
        return $this->hasMany(rider::class);
    }


    public function isVendor()
    {
        return $this->requestsToBeVendor()?->where(['status' => 'Active'])->first();
    }
    public function isReseller()
    {
        return $this->requestsToBeReseller()?->where(['status' => 'Active'])->first() ? true : false;
    }
    public function isRider()
    {
        return $this->requestsToBeRider()?->where(['status' => 'Active'])->first();
    }

    public function resellerShop()
    {
        return $this->requestsToBeReseller()?->where(['status' => 'Active'])->first();
    }
    public function vendorShop()
    {
        return $this->requestsToBeVendor()?->where(['status' => 'Active'])->first();
    }


    private function papp()
    {
        return $this->hasMany(Product::class);
    }

    public function account_type()
    {
        $account = '';
        $roles = auth()->user()->getRoleNames();
        // dd($roles);
        if (count($roles) > 2) {
            $account = auth()->user()->active_nav;
        } else {

            $account = auth()->user()->isVendor() ? 'vendor' : 'reseller';
        }

        return $account;
    }


    public function myProducts()
    {
        return $this->papp()->where(['belongs_to_type' => $this->account_type()]);
    }

    // public function myProductsAsVendor()
    // {
    //     return $this->papp()->where(['belongs_to_type' => 'vendor']);
    // }
    // public function myProductsAsReseller()
    // {
    //     return $this->papp()->where(['belongs_to_type' => 'reseller']);
    // }


    private function myCt()
    {
        return $this->hasMany(Category::class);
    }

    public function myCategory()
    {
        return $this->myCt()->where(['belongs_to' => $this->account_type()]);
    }

    // public function myCategoryAsVendor()
    // {
    //     return $this->myCt()->where(['belongs_to' => 'vendor']);
    // }
    // public function myCategoryAsReseller()
    // {
    //     return $this->myCt()->where(['belongs_to' => 'reseller']);
    // }


    private function uct()
    {
        return $this->hasMany(cart::class);
    }

    public function myCarts()
    {
        return $this->uct()->where(['user_type' => 'user']);
    }

    public function myCartsAsReseller()
    {
        return $this->uct()->where(['user_type' => 'reseller']);
    }

    private function myOr()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    public function myOrderAsUser()
    {
        return $this->myOr()->where(
            [
                'belongs_to_type' => 'reseller'
            ]
        );
    }

    public function myOrdersAsReseller()
    {
        return $this->myOr()->where(
            [
                'user_type' => 'reseller'
            ]
        );
    }


    public function orderToMe()
    {
        // return $this->hasMany(Order::class);
        return Order::where(['belongs_to' => auth()->user()->id]);
    }


    /**
     * vip package
     * @return vip 
     */
    public function subscription()
    {
        return $this->hasOne(vip::class);
    }


    public function myWithdraw()
    {
        return $this->hasMany(Withdraw::class);
    }


    public function myDeposit()
    {
        return $this->hasMany(userDeposit::class);
    }

    public function cod()
    {
        return $this->hasMany(cod::class);
    }
}
