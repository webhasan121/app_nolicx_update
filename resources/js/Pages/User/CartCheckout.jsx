import { useForm } from "@inertiajs/react";
import Container from "../../components/dashboard/Container";
import NavLink from "../../components/NavLink";
import UserDash from "../../components/user/dash/UserDash";
import { useEffect, useState } from "react";
import DashboardForeach from "../../components/DashboardForeach";
import Table from "../../components/dashboard/table/Table";
import InputLabel from "../../components/InputLabel";
import InputField from "../../components/InputField";
import Hr from "../../components/Hr";
import PrimaryButton from "../../components/PrimaryButton";
import InputFile from "../../components/InputFile";
export default function CartCheckout({ carts = [], states = [] }) {
    const { data, setData, post, errors, processing } = useForm({
        phone: "",
        house_no: "",
        road_no: "",
        location: "",
        area_condition: 'Dhaka',
        district: "",
        upozila: "",
        delevery: "cash",
    });

    const [cities, setCities] = useState([]);



    useEffect(() => {
        if (data.district) {
            axios.get(`/user/cities/${data.district}`).then((res) => {
                setCities(res.data);
            });
        }
    }, [data.district]);


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


    return (
        <UserDash>
            <Container>
                <div>
                    <div className="text-3xl">Checkout</div>

                    <div>
                        view and order your cart produtct.{" "}
                        <NavLink href={route("carts.view")}>
                            <i className="fa-solid fa-up-right-from-square me-2"></i>{" "}
                            carts
                        </NavLink>
                    </div>

                    <div>
                        <div>
                            <b>Notice:</b> You're order from Multiple Shops
                        </div>

                        <div className="text-xs">
                            You have added product from more than one shop.
                            Please note that, items from different shops are
                            shipped seperately, which will result in{" "}
                            <strong>Multiple Shipping Charges.</strong>
                            <br />
                            To reduce delivery cost and ensure a smoother
                            experience, we recommend placing orders from{" "}
                            <strong>a single shop at a time.</strong> Review the
                            shop name to your cart before placing orders.
                        </div>
                    </div>
                </div>

                <>
                    <DashboardForeach data={carts}>
                        <div className="overflow-hidden overflow-x-scroll">
                            <table className="w-full mb-2 border border-collapse">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th></th>
                                        <th>Shop</th>
                                        <th>Quantity</th>
                                        <th>Attr</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    {carts.map((cart, index) => {
                                        const sprice = cart.price * cart.qty;

                                        return (
                                            <tr key={cart.id}>
                                                <td>{index + 1}</td>

                                                <td className="text-sm">
                                                    <NavLink
                                                        href={route(
                                                            "products.details",
                                                            {
                                                                id: cart.product_id,
                                                                slug:
                                                                    cart.slug ??
                                                                    "product",
                                                            },
                                                        )}
                                                    >
                                                        <div className="items-center block lg:flex">
                                                            <img
                                                                width="30"
                                                                height="30"
                                                                src={`/storage/${cart.image}`}
                                                                alt=""
                                                            />

                                                            <span className="ml-1 text-xs text-wrap">
                                                                {cart.name ||
                                                                    "N/A"}
                                                            </span>
                                                        </div>
                                                    </NavLink>
                                                </td>

                                                <td>
                                                    <NavLink>
                                                        <div className="px-1 text-xs">
                                                            {cart.shop_name ??
                                                                "N/A"}
                                                        </div>
                                                    </NavLink>
                                                </td>

                                                <td>
                                                    <div
                                                        className="flex justify-between px-1 py-0 text-center border rounded"
                                                        style={{
                                                            width: "120px",
                                                        }}
                                                    >
                                                        <button
                                                            className="p-1 text-md"
                                                            onClick={() =>
                                                                decreaseQuantity(
                                                                    cart.id,
                                                                )
                                                            }
                                                        >
                                                            -
                                                        </button>

                                                        <input
                                                            style={{
                                                                width: "50px",
                                                            }}
                                                            className="py-0 text-sm text-center border-0 rounded w-sm"
                                                            value={cart.qty}
                                                            disabled
                                                        />

                                                        <button
                                                            className="p-1 text-md"
                                                            onClick={() =>
                                                                increaseQuantity(
                                                                    cart.id,
                                                                )
                                                            }
                                                        >
                                                            +
                                                        </button>
                                                    </div>
                                                </td>

                                                <td>
                                                    {cart.attr_name && (
                                                        <div>
                                                            <InputLabel className="text-xs">
                                                                {cart.attr_name}
                                                            </InputLabel>

                                                            <select
                                                                className="text-sm border-gray-300 rounded"
                                                                value={
                                                                    cart.size ||
                                                                    ""
                                                                }
                                                                onChange={(e) =>
                                                                    setData(
                                                                        `carts.${index}.size`,
                                                                        e.target
                                                                            .value,
                                                                    )
                                                                }
                                                            >
                                                                {cart.attr_values?.map(
                                                                    (
                                                                        attr,
                                                                        i,
                                                                    ) => (
                                                                        <option
                                                                            key={
                                                                                i
                                                                            }
                                                                            value={
                                                                                attr
                                                                            }
                                                                        >
                                                                            {
                                                                                attr
                                                                            }
                                                                        </option>
                                                                    ),
                                                                )}
                                                            </select>
                                                        </div>
                                                    )}
                                                </td>

                                                <td className="text-nowrap">
                                                    {cart.price} x {cart.qty} ={" "}
                                                    {sprice}
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>

                                <tfoot className="bg-gray-200">
                                    <tr>
                                        <td colSpan="2">Price</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td className="bold">
                                            <strong>{tp} TK</strong>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colSpan="2">Shipping</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>

                                        <td>
                                            {data.delevery === "hand"
                                                ? "0 TK"
                                                : (shipping ?? "Depend On")}
                                        </td>
                                    </tr>

                                    <tr>
                                        <td colSpan="2">Total Payable</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>

                                        <td>{tp + (shipping ?? 0)} TK</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </DashboardForeach>

                    <br />
                    <br />

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
                                <InputFile
                                    label="District"
                                    name="state"
                                    error="district"
                                    errors={errors}
                                >
                                    <select
                                        value={data.district || ""}
                                        onChange={(e) =>
                                            setData("district", e.target.value)
                                        }
                                        id="states"
                                        className="w-full rounded-md "
                                    >
                                        <option value="">
                                            {" "}
                                            -- Select State --
                                        </option>

                                        {states.map((state) => (
                                            <option
                                                key={state.id}
                                                value={state.id}
                                            >
                                                {state.name}
                                            </option>
                                        ))}
                                    </select>
                                </InputFile>

                                <Hr />

                                <InputFile
                                    label="Upozila"
                                    name="city"
                                    error="upozila"
                                    errors={errors}
                                >
                                    <select
                                        value={data.upozila || ""}
                                        onChange={(e) =>
                                            setData("upozila", e.target.value)
                                        }
                                        id="states"
                                        className="w-full rounded-md "
                                    >
                                        <option value="">
                                            {" "}
                                            -- Select City --
                                        </option>

                                        {cities?.map((item) => (
                                            <option
                                                key={item.id}
                                                value={item.id}
                                            >
                                                {item.name}
                                            </option>
                                        ))}
                                    </select>
                                </InputFile>

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
