import { useEffect, useState } from "react";
import { useForm } from "@inertiajs/react";
import Container from "../../components/dashboard/Container";
import SectionSection from "../../components/dashboard/section/Section";
import SectionHeader from "../../components/dashboard/section/Header";
import SectionInner from "../../components/dashboard/section/Inner";
import Hr from "../../components/Hr";
import InputFile from "../../components/InputFile";
import InputLabel from "../../components/InputLabel";
import PrimaryButton from "../../components/PrimaryButton";
import ProductSingle from "../../components/client/ProductSingle";
import TextInput from "../../components/TextInput";
import UserLayout from "../../Layouts/User/App";

export default function Order({ product, states = [], initialPrice = 0 }) {
    const attrValues = product?.attr?.value
        ? String(product.attr.value)
              .split(",")
              .map((v) => v.trim())
              .filter(Boolean)
        : [];

    const deliveryDefault = product?.cod
        ? "cash"
        : product?.courier
          ? "courier"
          : product?.hand
            ? "hand"
            : "";

    const { data, setData, post, processing, errors } = useForm({
        size: attrValues.length ? "" : "Size Less",
        quantity: 1,
        phone: "",
        district: "",
        upozila: "",
        location: "",
        house_no: "",
        road_no: "",
        area_condition: "Dhaka",
        delevery: deliveryDefault,
    });

    const [cities, setCities] = useState([]);
    const quantity = Number(data.quantity) > 0 ? Number(data.quantity) : 1;
    const price = Number(initialPrice || 0);
    const total = price * quantity;
    const shipping =
        data.delevery === "hand"
            ? 0
            : data.area_condition === "Dhaka"
              ? Number(product.shipping_in_dhaka || 0)
              : Number(product.shipping_out_dhaka || 0);

    useEffect(() => {
        if (!data.district) {
            setCities([]);
            setData("upozila", "");
            return;
        }

        axios
            .get(`/product/order/location/cities/${data.district}`)
            .then((response) => {
                setCities(response.data || []);
            });
    }, [data.district]);

    useEffect(() => {
        if (data.delevery === "hand") {
            setData("area_condition", "Dhaka");
        }
    }, [data.delevery]);

    const submit = (e) => {
        e.preventDefault();
        post(route("product.makeOrder.store", { id: product.id, slug: product.slug }));
    };

    return (
        <UserLayout title={product?.title}>
            <Container>
                <SectionSection>
                    <ProductSingle product={product} />
                </SectionSection>

                <Hr />

                <SectionSection>
                    <SectionHeader
                        title="Order Now"
                        content={<strong>Single Product: {total ?? 0} TK</strong>}
                    />
                    <Hr />
                    <SectionInner>
                        <form onSubmit={submit}>
                            <div className="items-start justify-between md:flex">
                                <div className="top-0 w-48 p-3 pr-2 text-white bg-indigo-900 rounded shadow md:sticky">
                                    <div className="p-4 rounded shadow">
                                        <div>
                                            <div className="text-xs">Product</div>
                                            <div className="text-sm">{product.name}</div>
                                        </div>
                                        <Hr />
                                        <div className="flex justify-between">
                                            <div className="text-xs">Price</div>
                                            <div className="text-sm bold">{price}</div>
                                        </div>
                                        <Hr />
                                        <div className="flex justify-between">
                                            <div className="text-xs">Unite</div>
                                            <div className="text-sm bold">{quantity}</div>
                                        </div>
                                        <Hr />
                                        <div className="flex justify-between">
                                            <div className="text-xs">Shipping</div>
                                            <div className="text-sm bold">{shipping}</div>
                                        </div>
                                        <Hr />
                                        <div className="flex justify-between">
                                            <div className="text-xs">Total</div>
                                            <div className="text-sm bold">{shipping + total}</div>
                                        </div>
                                    </div>
                                </div>

                                <div className="w-full md:w-1/2">
                                    {attrValues.length > 0 ? (
                                        <div className="md:flex">
                                            <InputLabel htmlFor="size" style={{ width: "350px" }}>
                                                {product?.attr?.name}
                                            </InputLabel>
                                            <select
                                                id="size"
                                                value={data.size}
                                                onChange={(e) => setData("size", e.target.value)}
                                                className="w-full border-gray-300 rounded"
                                                required
                                            >
                                                <option value="">-- select --</option>
                                                {attrValues.map((attr) => (
                                                    <option key={attr} value={attr}>
                                                        {attr}
                                                    </option>
                                                ))}
                                            </select>
                                        </div>
                                    ) : null}

                                    <InputFile label="Quantity" error="quantity" name="quantity" errors={errors}>
                                        <TextInput
                                            className="w-full"
                                            type="number"
                                            min="1"
                                            value={data.quantity}
                                            onChange={(e) => setData("quantity", e.target.value)}
                                        />
                                    </InputFile>

                                    <InputFile label="Phone" error="phone" name="phone" errors={errors}>
                                        <TextInput
                                            type="text"
                                            placeholder="+8801100 000000"
                                            className="w-full"
                                            value={data.phone}
                                            onChange={(e) => setData("phone", e.target.value)}
                                        />
                                    </InputFile>

                                    <InputFile label="State" name="state" error="district" errors={errors}>
                                        <select
                                            value={data.district}
                                            onChange={(e) => setData("district", e.target.value)}
                                            id="states"
                                            className="w-full rounded-md"
                                        >
                                            <option value="">-- Select State --</option>
                                            {states.map((state) => (
                                                <option key={state.id} value={state.id}>
                                                    {state.name}
                                                </option>
                                            ))}
                                        </select>
                                    </InputFile>

                                    <Hr />

                                    <InputFile label="City" name="city" error="upozila" errors={errors}>
                                        <select
                                            value={data.upozila}
                                            onChange={(e) => setData("upozila", e.target.value)}
                                            id="cities"
                                            className="w-full rounded-md"
                                        >
                                            <option value="">-- Select City --</option>
                                            {cities.map((item) => (
                                                <option key={item.id} value={item.id}>
                                                    {item.name}
                                                </option>
                                            ))}
                                        </select>
                                    </InputFile>

                                    <div>
                                        <InputFile label="House No" error="house_no" name="house_no" errors={errors}>
                                            <TextInput
                                                className="w-full"
                                                type="text"
                                                placeholder="House No"
                                                value={data.house_no}
                                                onChange={(e) => setData("house_no", e.target.value)}
                                            />
                                        </InputFile>

                                        <InputFile label="Road No" error="road_no" name="road_no" errors={errors}>
                                            <TextInput
                                                className="w-full"
                                                type="text"
                                                placeholder="Road No"
                                                value={data.road_no}
                                                onChange={(e) => setData("road_no", e.target.value)}
                                            />
                                        </InputFile>
                                    </div>

                                    <Hr />

                                    <div className="mt-4">
                                        <InputLabel>Your Full Address</InputLabel>
                                        {errors.location ? (
                                            <div className="text-sm text-red-600">{errors.location}</div>
                                        ) : null}
                                        <textarea
                                            value={data.location}
                                            onChange={(e) => setData("location", e.target.value)}
                                            cols="3"
                                            className="w-full rounded-md"
                                            placeholder="Your Full Address With Contact Number"
                                        />
                                    </div>

                                    <Hr />

                                    <div className="p-3 mt-4 bg-indigo-200 rounded">
                                        <InputLabel>Develery Option</InputLabel>

                                        {product?.shipping_note ? (
                                            <div className="flex p-1 bg-indigo-900 rounded-lg shadow bg-gray-50">
                                                <i className="block h-auto p-2 rounded shadow-xl bg-gray-50 fas fa-bell"></i>
                                                <p className="p-2 text-xs text-white">{product.shipping_note}</p>
                                            </div>
                                        ) : null}

                                        <div className="px-2">
                                            {product?.cod ? (
                                                <>
                                                    <div className="flex items-start py-3">
                                                        <input
                                                            type="radio"
                                                            value="cash"
                                                            checked={data.delevery === "cash"}
                                                            onChange={(e) => setData("delevery", e.target.value)}
                                                            style={{ width: "20px", height: "20px" }}
                                                            className="m-0 mr-3"
                                                        />
                                                        <InputLabel>
                                                            Cash-On Delivery
                                                            <p className="text-xs">Get home delivery. Get the product and pay.</p>
                                                        </InputLabel>
                                                    </div>
                                                    <hr />
                                                </>
                                            ) : null}

                                            {product?.courier ? (
                                                <>
                                                    <div className="flex items-start py-3">
                                                        <input
                                                            type="radio"
                                                            value="courier"
                                                            checked={data.delevery === "courier"}
                                                            onChange={(e) => setData("delevery", e.target.value)}
                                                            style={{ width: "20px", height: "20px" }}
                                                            className="m-0 mr-3"
                                                        />
                                                        <InputLabel>
                                                            Courier
                                                            <p className="text-xs">
                                                                You wish to take your order via a courier service. Check your nearest courier provider and give us the correct address.
                                                            </p>
                                                        </InputLabel>
                                                    </div>
                                                    <hr />
                                                </>
                                            ) : null}

                                            {product?.hand ? (
                                                <div className="flex items-start py-3">
                                                    <input
                                                        type="radio"
                                                        value="hand"
                                                        checked={data.delevery === "hand"}
                                                        onChange={(e) => setData("delevery", e.target.value)}
                                                        style={{ width: "20px", height: "20px" }}
                                                        className="m-0 mr-3"
                                                    />
                                                    <div>
                                                        <InputLabel>Hand to Hand</InputLabel>
                                                        <p className="text-xs">
                                                            You plan to take the product direct form seller shop. Great ! save your shipping coast.
                                                        </p>
                                                    </div>
                                                </div>
                                            ) : null}

                                            {errors.delevery ? (
                                                <div className="text-xs text-red-900">
                                                    <strong>{errors.delevery}</strong>
                                                </div>
                                            ) : null}
                                        </div>

                                        <Hr />

                                        <div className="px-2 rounded shadow bg-gray-50">
                                            <div className={`flex items-start py-3 ${data.delevery === "hand" ? "hidden" : ""}`}>
                                                <input
                                                    type="radio"
                                                    value="Dhaka"
                                                    checked={data.area_condition === "Dhaka"}
                                                    onChange={(e) => setData("area_condition", e.target.value)}
                                                    style={{ width: "20px", height: "20px" }}
                                                    className="p-0 m-0 mr-3"
                                                />
                                                <InputLabel className="p-0 m-0">Inside Dhaka</InputLabel>
                                            </div>

                                            <hr />

                                            <div className={`flex items-start py-3 ${data.delevery === "hand" ? "hidden" : ""}`}>
                                                <input
                                                    type="radio"
                                                    value="Other"
                                                    checked={data.area_condition === "Other"}
                                                    onChange={(e) => setData("area_condition", e.target.value)}
                                                    style={{ width: "20px", height: "20px" }}
                                                    className="p-0 m-0 mr-3"
                                                />
                                                <InputLabel className="p-0 m-0">Outside of Dhaka</InputLabel>
                                            </div>

                                            {data.delevery === "hand" ? (
                                                <div className="bg-green-50">
                                                    <strong>
                                                        Shop : {product?.owner?.shop?.shop_name_en ?? "N/A"}{" "}
                                                        <a
                                                            className="px-2 rounded-xl bg-gray-50"
                                                            href={route("shops.visit", {
                                                                id: product?.owner?.shop?.id,
                                                                name: product?.owner?.shop?.shop_name_en,
                                                            })}
                                                        >
                                                            visit
                                                        </a>
                                                    </strong>
                                                </div>
                                            ) : null}
                                        </div>

                                        <span className="text-xs">
                                            Define delevary type you chose. You might be consider extra delevary charged for{" "}
                                            <strong>Home Delevary</strong> outside of Dhaka
                                        </span>
                                    </div>

                                    <br />
                                    <PrimaryButton disabled={processing}>
                                        Confirm Order
                                    </PrimaryButton>
                                </div>
                            </div>
                        </form>
                    </SectionInner>
                </SectionSection>
            </Container>
        </UserLayout>
    );
}
