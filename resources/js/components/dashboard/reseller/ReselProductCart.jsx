import { useForm } from "@inertiajs/react";
import { useMemo, useState } from "react";
import Modal from "../../Modal";
import Hr from "../../Hr";
import InputField from "../../InputField";
import InputLabel from "../../InputLabel";
import NavLink from "../../NavLink";
import PrimaryButton from "../../PrimaryButton";
import DistrictUpozilaSelect from "../../DistrictUpozilaSelect";

function SelectField({ label, name, error, children }) {
    return (
        <div className="my-3">
            <InputLabel
                htmlFor={name}
                className="block text-sm font-medium text-gray-700"
            >
                {label}
            </InputLabel>
            <div className="mt-1">{children}</div>
            {error && <div className="text-sm text-red-600">{error}</div>}

        </div>
    );
}

export default function ReselProductCart({ product }) {
    const [showOrderModal, setShowOrderModal] = useState(false);

    const form = useForm({
        name: "",
        phone: "",
        district: "",
        upozila: "",
        location: "",
        house_no: "",
        road_no: "",
        area_condition: "",
        delevery: "",
        quantity: "",
        attr: "",
    });

    const discountPercent = useMemo(() => {
        if (!product?.offer_type || !product?.price) return 0;
        const diff = product.price - (product.discount ?? 0);
        return Math.round(((diff / product.price) * 100) * 10) / 10;
    }, [product]);

    const attrValues = useMemo(() => {
        const value = product?.attr?.value ?? "";
        if (!value) return [];
        return value.split(",").map((item) => item.trim()).filter(Boolean);
    }, [product]);

    const quantityOptions = useMemo(() => {
        const unit = Number(product?.unit ?? 0);
        if (unit < 1) return [];
        return Array.from({ length: unit }, (_, i) => i + 1);
    }, [product]);

    const totalPrice =
        Number(form.data.quantity || 0) * Number(product?.total_price || 0);

    const updateField = (field, value) => {
        form.setData(field, value);

        if (form.errors[field]) {
            form.clearErrors(field);
        }
    };

    const submitOrder = (e) => {
        e.preventDefault();
        form.post(route("reseller.resel-product.order", { product: product.id }), {
            onSuccess: () => {
                form.reset();
                form.clearErrors();
                setShowOrderModal(false);
            },
        });
    };

    const orderModal = (
        <Modal
            show={showOrderModal}
            onClose={() => setShowOrderModal(false)}
            maxWidth="md"
        >
            <div className="flex items-center justify-between p-3 border-b bold">
                <div>Purchase</div>
                <div className="text-lg bold">{product?.total_price} TK</div>
            </div>
            <div className="flex items-start justify-start p-5 mb-0 bg-gray-100">
                <div className="flex">
                    {product?.thumbnail_url ? (
                        <img
                            src={product.thumbnail_url}
                            className="w-12 h-12 mr-3 rounded shadow"
                            alt=""
                        />
                    ) : null}
                </div>
                <div>
                    <div className="text-lg bold">{product?.name ?? "N/A"}</div>
                    <div className="text-sm">
                        {product?.offer_type ? (
                            <div className="flex items-baseline gap-2">
                                <div className="bold">
                                    Price : {product?.total_price ?? "0"} TK
                                </div>
                                <div className="text-xs">
                                    <del>MRP : {product?.price ?? "0"} TK</del>
                                </div>
                                <div className="text-xs">
                                    {discountPercent}% off
                                </div>
                            </div>
                        ) : (
                            <div className="bold">
                                Pirce : {product?.price ?? "0"} TK
                            </div>
                        )}
                        <div className="text-xs">
                            Available Stock: {product?.unit ?? "0"}
                        </div>
                    </div>
                </div>
            </div>
            <div className="p-5 pt-0">
                <form onSubmit={submitOrder}>
                    <InputField
                        label="Name"
                        name="name"
                        value={form.data.name}
                        error={form.errors.name}
                        onChange={(e) => form.setData("name", e.target.value)}
                        onClearError={() => form.clearErrors("name")}
                    />
                    <InputField
                        label="Phone"
                        name="phone"
                        value={form.data.phone}
                        error={form.errors.phone}
                        onChange={(e) => form.setData("phone", e.target.value)}
                        onClearError={() => form.clearErrors("phone")}
                    />
                    <DistrictUpozilaSelect
                        district={form.data.district}
                        upozila={form.data.upozila}
                        errors={form.errors}
                        className=""
                        labelWidth="100%"
                        onDistrictChange={(value) => {
                            form.setData("district", value);
                            form.clearErrors("district");
                        }}
                        onUpozilaChange={(value) => {
                            form.setData("upozila", value);
                            form.clearErrors("upozila");
                        }}
                    />
                    <div className="my-3">
                        <InputLabel
                            htmlFor="full_address"
                            className="block text-sm font-medium text-gray-700"
                        >
                            Full Address
                        </InputLabel>
                        <textarea
                            id="full_address"
                            name="location"
                            cols="3"
                            className="w-full p-2 mt-1 rounded-md"
                            placeholder="Full Address"
                            value={form.data.location}
                            onChange={(e) =>
                                updateField("location", e.target.value)
                            }
                        ></textarea>
                        {form.errors.location && (
                            <div className="text-sm text-red-600">
                                {form.errors.location}
                            </div>
                        )}
                    </div>
                    <InputField
                        label="Road No"
                        name="road_no"
                        value={form.data.road_no}
                        error={form.errors.road_no}
                        onChange={(e) => form.setData("road_no", e.target.value)}
                        onClearError={() => form.clearErrors("road_no")}
                    />
                    <InputField
                        label="House No"
                        name="house_no"
                        value={form.data.house_no}
                        error={form.errors.house_no}
                        onChange={(e) =>
                            form.setData("house_no", e.target.value)
                        }
                        onClearError={() => form.clearErrors("house_no")}
                    />

                    <SelectField
                        label="Quantity"
                        name="quantity"
                        error={form.errors.quantity}
                    >
                        <select
                            id="quantity"
                            className="w-full py-2 border-gray-300 rounded-md shadow-sm"
                            value={form.data.quantity}
                            onChange={(e) =>
                                updateField("quantity", e.target.value)
                            }
                        >
                            <option value="">Select Quantity</option>
                            {quantityOptions.map((qty) => (
                                <option key={qty} value={qty}>
                                    {qty}
                                </option>
                            ))}
                        </select>
                    </SelectField>

                    <div className="p-2 bg-indigo-100">
                        <div className="text-xs">
                            {Number(product?.unit ?? 0) < 1
                                ? "Stock Out"
                                : `You can order maximum ${product?.unit ?? 0} item`}
                        </div>
                        <div className="flex items-center justify-between">
                            <div>Total</div>
                            <div>
                                {form.data.quantity || 0} *{" "}
                                {product?.total_price ?? 0} = {totalPrice}
                            </div>
                        </div>
                    </div>

                    <SelectField
                        label="Product Size/Attribute"
                        name="attr"
                        error={form.errors.attr}
                    >
                        <select
                            id="product_attr"
                            className="w-full py-2 border-gray-300 rounded-md shadow-sm"
                            value={form.data.attr}
                            onChange={(e) => updateField("attr", e.target.value)}
                        >
                            <option value="">Select Size/Attribute</option>
                            {attrValues.length ? (
                                attrValues.map((attr) => (
                                    <option key={attr} value={attr}>
                                        {attr}
                                    </option>
                                ))
                            ) : (
                                <option value="N/A">N/A</option>
                            )}
                        </select>
                    </SelectField>

                    <Hr />

                    <SelectField
                        label="Area Condition"
                        name="area_condition"
                        error={form.errors.area_condition}
                    >
                        <select
                            id="area_condition"
                            className="w-full py-2 border-gray-300 rounded-md shadow-sm"
                            value={form.data.area_condition}
                            onChange={(e) =>
                                updateField("area_condition", e.target.value)
                            }
                        >
                            <option value="">Select Area</option>
                            <option value="Dhaka">Inside Dhaka</option>
                            <option value="Other">Out side of Dhaka</option>
                        </select>
                    </SelectField>
                    <SelectField
                        label="Shipping"
                        name="delevery"
                        error={form.errors.delevery}
                    >
                        <select
                            id="delevery"
                            className="w-full py-2 border-gray-300 rounded-md shadow-sm"
                            value={form.data.delevery}
                            onChange={(e) =>
                                updateField("delevery", e.target.value)
                            }
                        >
                            <option value="">Shipping Type</option>
                            <option value="courier">Courier</option>
                            <option value="home">Home Delivery</option>
                            <option value="hand">Hand-To-Hand</option>
                        </select>
                    </SelectField>
                    <PrimaryButton>Order</PrimaryButton>
                </form>
            </div>
        </Modal>
    );

    return (
        <div>
            <div className="relative overflow-hidden bg-white rounded shadow">
                {product?.offer_type ? (
                    <div className="bg-orange-500 discount-badge ">
                        {discountPercent ?? 0}%
                    </div>
                ) : null}

                <div className="p-1 overflow-hidden shadow-md">
                    {product?.thumbnail_url ? (
                        <img
                            style={{ height: "120px" }}
                            src={product.thumbnail_url}
                            className="object-cover w-full"
                            alt="image"
                        />
                    ) : null}
                </div>

                <div className="flex flex-col justify-between p-2 bg-white h-34">
                    <NavLink
                        href={route("reseller.resel-product.veiw", {
                            pd: product?.id,
                        })}
                    >
                        <div className="text-sm product-title-clamp-3">
                            {product?.name ?? "N/A"}
                        </div>
                    </NavLink>

                    <div>
                        <div className="mb-3 text-md">
                            {product?.offer_type ? (
                                <>
                                    <div className="bold">
                                        {product?.discount ?? "0"} TK
                                    </div>
                                    <div className="text-xs">
                                        <del>{product?.price ?? "0"} TK</del>
                                    </div>
                                </>
                            ) : (
                                <div className="bold">
                                    {product?.price ?? "0"} TK
                                </div>
                            )}
                        </div>
                        <div className="flex items-center justify-center text-sm">
                            <Hr />
                            <PrimaryButton
                                className="flex justify-between w-full text-center"
                                type="button"
                                onClick={() => setShowOrderModal(true)}
                            >
                                Purchase{" "}
                                <i className="pl-2 fas fa-angle-right"></i>
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
            </div>

            {orderModal}
        </div>
    );
}
