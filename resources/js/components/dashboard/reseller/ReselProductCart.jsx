import { useForm } from "@inertiajs/react";
import { useMemo, useState } from "react";
import Modal from "../../Modal";
import Hr from "../../Hr";
import NavLink from "../../NavLink";
import PrimaryButton from "../../PrimaryButton";

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

    const submitOrder = (e) => {
        e.preventDefault();
        form.post(route("reseller.resel-product.order", { product: product.id }), {
            onSuccess: () => setShowOrderModal(false),
        });
    };

    return (
        <div>
            <div className="bg-white rounded shadow overflow-hidden relative">
                {product?.offer_type ? (
                    <div className="discount-badge bg-orange-600 ">
                        {discountPercent ?? 0}%
                    </div>
                ) : null}

                <div className="overflow-hidden shadow-md p-1">
                    {product?.thumbnail_url ? (
                        <img
                            style={{ height: "120px" }}
                            src={product.thumbnail_url}
                            className="w-full object-cover"
                            alt="image"
                        />
                    ) : null}
                </div>

                <div className="p-2 bg-white h-34 flex flex-col justify-between">
                    <NavLink
                        href={route("reseller.resel-product.veiw", {
                            pd: product?.id,
                        })}
                    >
                        <div className="text-sm">{product?.name ?? "N/A"}</div>
                    </NavLink>

                    <div>
                        <div className="text-md mb-3">
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
                        <div className="flex justify-center items-center text-sm">
                            <Hr />
                            <PrimaryButton
                                className="text-center w-full flex justify-between"
                                type="button"
                                onClick={() => setShowOrderModal(true)}
                            >
                                Purchase{" "}
                                <i className="fas fa-angle-right pl-2"></i>
                            </PrimaryButton>
                        </div>
                    </div>
                </div>
            </div>

            <Modal
                show={showOrderModal}
                onClose={() => setShowOrderModal(false)}
                maxWidth="md"
            >
                <div className="p-3 bold border-b flex justify-between items-center">
                    <div>Purchase</div>
                    <div className="bold text-lg">{product?.total_price} TK</div>
                </div>
                <div className="flex items-start justify-start mb-3 p-5 bg-gray-100">
                    <div className="flex">
                        {product?.thumbnail_url ? (
                            <img
                                src={product.thumbnail_url}
                                className="w-12 h-12 rounded shadow mr-3"
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
                <div className="p-5">
                    <form onSubmit={submitOrder}>
                        <input
                            className="w-full rounded-md p-2 mb-2"
                            placeholder="Name"
                            value={form.data.name}
                            onChange={(e) => form.setData("name", e.target.value)}
                        />
                        <input
                            className="w-full rounded-md p-2 mb-2"
                            placeholder="Phone"
                            value={form.data.phone}
                            onChange={(e) => form.setData("phone", e.target.value)}
                        />
                        <input
                            className="w-full rounded-md p-2 mb-2"
                            placeholder="District"
                            value={form.data.district}
                            onChange={(e) =>
                                form.setData("district", e.target.value)
                            }
                        />
                        <input
                            className="w-full rounded-md p-2 mb-2"
                            placeholder="Upozila"
                            value={form.data.upozila}
                            onChange={(e) => form.setData("upozila", e.target.value)}
                        />
                        <textarea
                            className="w-full rounded-md p-2 mb-2"
                            placeholder="Full Address"
                            value={form.data.location}
                            onChange={(e) =>
                                form.setData("location", e.target.value)
                            }
                        ></textarea>
                        <input
                            className="w-full rounded-md p-2 mb-2"
                            placeholder="Road No"
                            value={form.data.road_no}
                            onChange={(e) => form.setData("road_no", e.target.value)}
                        />
                        <input
                            className="w-full rounded-md p-2 mb-2"
                            placeholder="House No"
                            value={form.data.house_no}
                            onChange={(e) =>
                                form.setData("house_no", e.target.value)
                            }
                        />

                        <div className="mb-2">
                            <select
                                className="rounded-md py-1 border w-full"
                                value={form.data.quantity}
                                onChange={(e) =>
                                    form.setData("quantity", e.target.value)
                                }
                            >
                                <option value="">Select Quantity</option>
                                {quantityOptions.map((qty) => (
                                    <option key={qty} value={qty}>
                                        {qty}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div className="p-2 bg-indigo-100">
                            <div className="text-xs">
                                {Number(product?.unit ?? 0) < 1
                                    ? "Stock Out"
                                    : `You can order maximum ${product?.unit ?? 0} item`}
                            </div>
                            <div className="flex justify-between items-center">
                                <div>Total</div>
                                <div>
                                    {form.data.quantity || 0} *{" "}
                                    {product?.total_price ?? 0} = {totalPrice}
                                </div>
                            </div>
                        </div>

                        <div className="mb-2">
                            <select
                                className="rounded-md py-1 border w-full"
                                value={form.data.attr}
                                onChange={(e) => form.setData("attr", e.target.value)}
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
                        </div>

                        <Hr />

                        <div className="mb-2">
                            <select
                                className="rounded py-1 w-full"
                                value={form.data.area_condition}
                                onChange={(e) =>
                                    form.setData("area_condition", e.target.value)
                                }
                            >
                                <option value="">Select Area</option>
                                <option value="Dhaka">Inside Dhaka</option>
                                <option value="Other">Out side of Dhaka</option>
                            </select>
                        </div>
                        <div className="mb-2">
                            <select
                                className="rounded py-1 w-full"
                                value={form.data.delevery}
                                onChange={(e) =>
                                    form.setData("delevery", e.target.value)
                                }
                            >
                                <option value="">Shipping Type</option>
                                <option value="Courier">Courier</option>
                                <option value="Home">Home Delivery</option>
                                <option value="Hand">Hand-To-Hand</option>
                            </select>
                        </div>
                        <PrimaryButton>Order</PrimaryButton>
                    </form>
                </div>
            </Modal>
        </div>
    );
}
