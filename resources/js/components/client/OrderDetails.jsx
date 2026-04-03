import InputField from "../InputField";
import InputLabel from "../InputLabel";
import PrimaryButton from "../PrimaryButton";

export default function OrderDetails({
    product,
    data,
    setData,
    errors = {},
    onSubmit,
}) {
    if (!product) return null;

    const attrValues = product?.attr?.value
        ? String(product.attr.value)
              .split(",")
              .map((v) => v.trim())
              .filter(Boolean)
        : [];

    return (
        <form onSubmit={onSubmit}>
            <div className="md:flex">
                <div>
                    {attrValues.length > 0 && (
                        <div>
                            <InputLabel htmlFor="size" className="w-[250px]">
                                {product?.attr?.name}
                            </InputLabel>
                            <select
                                id="size"
                                className="border-gray-300 rounded"
                                value={data.size || ""}
                                onChange={(e) => setData("size", e.target.value)}
                                required
                            >
                                <option value="">select size</option>
                                {attrValues.map((attr) => (
                                    <option key={attr} value={attr}>
                                        {attr}
                                    </option>
                                ))}
                            </select>
                            {errors.size && <strong>{errors.size}</strong>}
                        </div>
                    )}

                    <InputField
                        label="Your Name"
                        name="name"
                        value={data.name || ""}
                        onChange={(e) => setData("name", e.target.value)}
                        error={errors.name}
                    />
                    <InputField
                        type="number"
                        min="1"
                        label="Quantity"
                        name="quantity"
                        value={data.quantity || ""}
                        onChange={(e) => setData("quantity", e.target.value)}
                        error={errors.quantity}
                    />
                </div>

                <div>
                    <div>
                        <InputLabel>Your Full Address</InputLabel>
                        {errors.location && <div className="text-sm text-red-600">{errors.location}</div>}
                        <textarea
                            className="w-full rounded"
                            cols="5"
                            placeholder="Address"
                            value={data.location || ""}
                            onChange={(e) => setData("location", e.target.value)}
                        />
                    </div>
                    <InputField
                        label="Your Active Phone"
                        name="phone"
                        value={data.phone || ""}
                        onChange={(e) => setData("phone", e.target.value)}
                        error={errors.phone}
                    />
                </div>
            </div>

            <PrimaryButton>Confirm Order</PrimaryButton>
        </form>
    );
}

