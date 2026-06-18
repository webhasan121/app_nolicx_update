import { useForm } from "@inertiajs/react";
import Container from "../../components/dashboard/Container";
import NavLink from "../../components/NavLink";
import UserDash from "../../components/user/dash/UserDash";
import InputLabel from "../../components/InputLabel";
import InputField from "../../components/InputField";
import Hr from "../../components/Hr";
import PrimaryButton from "../../components/PrimaryButton";
import InputFile from "../../components/InputFile";
import DistrictUpozilaSelect from "../../components/DistrictUpozilaSelect";
import CartSummaryPanel from "../../components/user/CartSummaryPanel";
export default function CartCheckout({ carts = [], states = [] }) {
    const { data, setData, post, errors, processing } = useForm({
        phone: "",
        house_no: "",
        road_no: "",
        location: "",
        area_condition: 'Dhaka',
        district: "",
        upozila: "",
        targeted_area: "",
        delevery: "cash",
    });

    const increaseQuantity = (id) => {
        post(route("cart.qty.increase", id));
    };

    const decreaseQuantity = (id) => {
        post(route("cart.qty.decrease", id));
    };

    const confirm = (e) => {
        e.preventDefault();
        post(route("user.carts.confirm"));
    };

    const tp = carts.reduce((t, c) => t + c.price * c.qty, 0);

     const shipping =
        data.delevery === "hand" ? 0 : data.area_condition === "Dhaka" ? 80 : 120;
    const summaryItems = carts.map((cart) => ({
        ...cart,
        href: route("products.details", {
            id: cart.product_id,
            slug: cart.slug ?? "product",
        }),
        image: cart.image ? `/storage/${cart.image}` : "",
        name: cart.name,
        shop: cart.shop_name,
        quantity: cart.qty,
        total: cart.price * cart.qty,
        priceText: `${cart.price} x ${cart.qty} = ${cart.price * cart.qty} TK`,
    }));
    const summaryNotice = (
        <>
            <strong>Notice:</strong> You're order from Multiple Shops. You have
            added product from more than one shop. Items from different shops are
            shipped separately, which may result in{" "}
            <strong>Multiple Shipping Charges.</strong> For lower delivery cost,
            place orders from <strong>a single shop at a time.</strong>
        </>
    );


    return (
        <UserDash>
            <Container>
                <>
                    <CartSummaryPanel
                        title="Checkout"
                        subtitle={
                            <>
                                View and order your cart product.{" "}
                                <NavLink href={route("carts.view")}>
                                    <i className="fa-solid fa-up-right-from-square me-1"></i>
                                    carts
                                </NavLink>
                            </>
                        }
                        notice={summaryNotice}
                        items={summaryItems}
                        totals={[
                            { label: "Price", value: `${tp} TK` },
                            {
                                label: "Shipping",
                                value:
                                    data.delevery === "hand"
                                        ? "0 TK"
                                        : `${shipping ?? "Depend On"} TK`,
                            },
                            {
                                label: "Total Payable",
                                value: `${tp + (shipping ?? 0)} TK`,
                                emphasis: true,
                            },
                        ]}
                        renderQuantity={(cart) => (
                            <div className="inline-flex items-center overflow-hidden border border-gray-200 rounded-md bg-white shadow-sm">
                                <button
                                    type="button"
                                    className="flex items-center justify-center w-9 h-9 text-gray-600 hover:bg-gray-100"
                                    onClick={() => decreaseQuantity(cart.id)}
                                >
                                    -
                                </button>
                                <input
                                    className="w-12 h-9 p-0 text-sm text-center border-0 border-x border-gray-200"
                                    value={cart.qty}
                                    disabled
                                />
                                <button
                                    type="button"
                                    className="flex items-center justify-center w-9 h-9 text-gray-600 hover:bg-gray-100"
                                    onClick={() => increaseQuantity(cart.id)}
                                >
                                    +
                                </button>
                            </div>
                        )}
                        renderAttributes={(cart, index) =>
                            cart.attr_name ? (
                                <div className="min-w-[120px]">
                                    <InputLabel className="text-xs">
                                        {cart.attr_name}
                                    </InputLabel>
                                    <select
                                        className="w-full text-sm border-gray-300 rounded-md"
                                        value={cart.size || ""}
                                        onChange={(e) =>
                                            setData(
                                                `carts.${index}.size`,
                                                e.target.value,
                                            )
                                        }
                                    >
                                        {cart.attr_values?.map((attr, i) => (
                                            <option key={i} value={attr}>
                                                {attr}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            ) : (
                                "-"
                            )
                        }
                    />

                    <div className="h-4" />

                    <div className="p-3 m-2 bg-white rounded-md lg:w-1/2">
                        <form onSubmit={confirm} className="w-full">
                            <InputField
                                label="Your Active Phone"
                                name="phone"
                                value={data.phone}
                                error={errors.phone}
                                onChange={(e) =>
                                    setData("phone", e.target.value)
                                }
                            />

                            <div className="px-2 bg-gray-200">
                                <div className="flex items-center py-3">
                                    <input
                                        type="radio"
                                        value="Dhaka"
                                        style={{
                                            width: "20px",
                                            height: "20px",
                                        }}
                                        className="mb-0 mr-3"
                                        checked={
                                            data.area_condition === "Dhaka"
                                        }
                                        onChange={(e) =>
                                            setData(
                                                "area_condition",
                                                e.target.value,
                                            )
                                        }
                                    />

                                    <InputLabel>Inside Dhaka</InputLabel>
                                </div>

                                <Hr />

                                <div className="flex items-center py-3">
                                    <input
                                        type="radio"
                                        value="Other"
                                        style={{
                                            width: "20px",
                                            height: "20px",
                                        }}
                                        className="mb-0 mr-3"
                                        checked={
                                            data.area_condition === "Other"
                                        }
                                        onChange={(e) =>
                                            setData(
                                                "area_condition",
                                                e.target.value,
                                            )
                                        }
                                    />

                                    <InputLabel>Outside of Dhaka</InputLabel>
                                </div>
                            </div>

                            <div className="mt-4">
                                <InputLabel>Your Full Address</InputLabel>



                                <textarea
                                    className="w-full rounded"
                                    placeholder="Address"
                                    value={data.location}
                                    onChange={(e) =>
                                        setData("location", e.target.value)
                                    }
                                />
                                 {errors.location && (
                                    <div className="text-sm text-red-600">
                                        {errors.location}
                                    </div>
                                )}
                            </div>

                            <Hr />

                            <div className="p-1 mt-4 bg-indigo-200 rounded">
                                <InputLabel>Develery Option</InputLabel>

                                <div className="px-2 bg-gray-200">
                                    <div className="flex items-start py-3">

                                        <input
                                            type="radio"
                                            value="cash"
                                            style={{
                                                width: "20px",
                                                height: "20px",
                                            }}
                                            className="mr-3"
                                            checked={data.delevery === "cash"}
                                            onChange={(e) =>
                                                setData(
                                                    "delevery",
                                                    e.target.value,
                                                )
                                            }
                                        />

                                        <InputLabel>
                                            Cash-On Delivery
                                            <p className="text-xs">
                                                Get home delivery. Get the
                                                product and pay.
                                            </p>
                                        </InputLabel>
                                    </div>

                                    <Hr />

                                    <div className="flex items-start py-3">
                                        <input
                                            type="radio"
                                            value="courier"
                                            style={{
                                                width: "20px",
                                                height: "20px",
                                            }}
                                            className="mr-3"
                                            checked={
                                                data.delevery === "courier"
                                            }
                                            onChange={(e) =>
                                                setData(
                                                    "delevery",
                                                    e.target.value,
                                                )
                                            }
                                        />

                                        <InputLabel>
                                            Courier
                                            <p className="text-xs">
                                                You wish to take your order via
                                                a courier service. Check your
                                                nearest courier provider and
                                                give us the correct address.
                                            </p>
                                        </InputLabel>
                                    </div>

                                    <Hr />

                                    <div className="flex items-start py-3">
                                        <input
                                            type="radio"
                                            value="hand"
                                            style={{
                                                width: "20px",
                                                height: "20px",
                                            }}
                                            className="mr-3"
                                            checked={data.delevery === "hand"}
                                            onChange={(e) =>
                                                setData(
                                                    "delevery",
                                                    e.target.value,
                                                )
                                            }
                                        />

                                        <div>
                                            <InputLabel>
                                                Hand to Hand
                                            </InputLabel>

                                            <p className="text-xs">
                                                You plan to take the product
                                                direct form seller shop. Great !
                                                save your shipping coast.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div className="text-xs">
                                    Define delevary type you chose. You might be
                                    consider extra delevary charged for{" "}
                                    <strong>Cash-On Delivery</strong> outside of
                                    Dhaka
                                </div>
                            </div>
                            <div className="w-full">
                                <DistrictUpozilaSelect
                                    district={data.district}
                                    upozila={data.upozila}
                                    states={states}
                                    errors={errors}
                                    onDistrictChange={(value) => setData("district", value)}
                                    onUpozilaChange={(value) => setData("upozila", value)}
                                />

                                <Hr />

                                <InputField
                                    inputClass="w-full"
                                    className="mb-1"
                                    value={data.targeted_area}
                                    onChange={(e) =>
                                        setData("targeted_area", e.target.value)
                                    }
                                    label="Targeted Area"
                                    error={errors?.targeted_area}
                                    name="targeted_area"
                                />

                                <Hr />

                                <div>
                                    <InputField
                                        inputClass="w-full"
                                        className="mb-1"
                                        value={data.house_no}
                                        onChange={(e) =>
                                            setData("house_no", e.target.value)
                                        }
                                        label="House No"
                                        error={errors?.house_no}
                                        name="house_no"
                                    />

                                    <InputField
                                        inputClass="w-full"
                                        className="mb-1"
                                        value={data.road_no}
                                        onChange={(e) =>
                                            setData("road_no", e.target.value)
                                        }
                                        label="Road No"
                                        error={errors?.road_no}
                                        name="road_no"
                                    />
                                </div>
                            </div>
                            <Hr />
                            <div className="text-start">
                                <PrimaryButton disabled={processing}>
                                    Confirm Order
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </>
            </Container>
        </UserDash>
    );
}
