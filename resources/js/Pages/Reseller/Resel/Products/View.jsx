import { Head, useForm } from "@inertiajs/react";
import { useMemo, useState } from "react";
import AppLayout from "../../../../Layouts/App";
import Modal from "../../../../components/Modal";
import Hr from "../../../../components/Hr";
import NavLink from "../../../../components/NavLink";
import PrimaryButton from "../../../../components/PrimaryButton";
import Container from "../../../../components/dashboard/Container";
import ReselProductCart from "../../../../components/dashboard/reseller/ReselProductCart";
import Section from "../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../components/dashboard/section/Header";
import SectionInner from "../../../../components/dashboard/section/Inner";

function renderCategoryOptions(categories = [], depth = 0) {
    return categories.flatMap((category) => [
        <option key={category.id} value={category.id}>
            {"-".repeat(depth ? depth * 2 : 0)}
            {depth ? " " : ""}
            {category.name}
        </option>,
        ...(category.children?.length
            ? renderCategoryOptions(category.children, depth + 1)
            : []),
    ]);
}

export default function View({
    product,
    categories = [],
    reselDefaults,
    shop,
    totalReselProducts = 0,
    ableToAdd = false,
}) {
    const [selectedImage, setSelectedImage] = useState(
        product?.thumbnail_url ?? ""
    );
    const [showConfirm, setShowConfirm] = useState(false);

    const form = useForm({
        resel_price: reselDefaults?.resel_price ?? "",
        resel_discount_price: reselDefaults?.resel_discount_price ?? "",
        is_resel_with_discount_price: false,
        reseller_category_id: "",
    });

    const discountPercent = useMemo(() => {
        if (!product?.offer_type || !product?.price) return 0;
        const diff = product.price - (product.discount ?? 0);
        return Math.round(((diff / product.price) * 100) || 0);
    }, [product]);

    const attrValues = useMemo(() => {
        const value = product?.attr?.value ?? "";
        if (!value) return [];
        return value.split(",").map((item) => item.trim()).filter(Boolean);
    }, [product]);

    const confirmClone = () => {
        form.post(
            route("reseller.resel-product.clone", { product: product.id }),
            { onSuccess: () => setShowConfirm(false) }
        );
    };

    const profit = useMemo(() => {
        const base = Number(product?.total_price ?? 0);
        if (form.data.is_resel_with_discount_price) {
            return Number(form.data.resel_discount_price || 0) - base;
        }
        return Number(form.data.resel_price || 0) - base;
    }, [
        form.data.is_resel_with_discount_price,
        form.data.resel_discount_price,
        form.data.resel_price,
        product,
    ]);

    return (
        <AppLayout title="Resel Product">
            <Head title="Resel Product" />

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="md:flex justify-between items-center">
                                <div className="text-md">
                                    Product Review for Resel
                                </div>
                                <div className="flex bg-indigo-900 border border-indigo-900 rounded-xl">
                                    <div
                                        className="px-2 bg-white"
                                        title="Total Resell Products"
                                    >
                                        {totalReselProducts}
                                    </div>
                                    <div
                                        className="px-2 text-white"
                                        title="Max Resell Products"
                                    >
                                        {shop?.max_resell_product ?? 0}
                                    </div>
                                </div>
                            </div>
                        }
                        content={
                            <div>
                                <div>
                                    wish to resel this product, just click on
                                    the button bellow
                                </div>
                                <div className="flex">
                                    {!ableToAdd ? (
                                        <div className="p-2 bg-red-200 text-red-800">
                                            You have reached the maximum number
                                            of products you can upload{" "}
                                            {shop?.max_resell_product ?? 0}.
                                            Please delete some products to add
                                            new ones or{" "}
                                            <div className="text-blue-600">
                                                upgrade your plan
                                            </div>
                                            .
                                        </div>
                                    ) : (
                                        <PrimaryButton
                                            type="button"
                                            onClick={() => setShowConfirm(true)}
                                        >
                                            <i className="fas fa-sync pr-2"></i>{" "}
                                            resell
                                        </PrimaryButton>
                                    )}
                                </div>
                            </div>
                        }
                    />
                    <Hr />

                    <SectionInner>
                        <div className="lg:flex justify-between item-start p-2">
                            <div className="w-full lg:w-1/2">
                                <div className="img-display">
                                    <div className="img-showcase relative">
                                        {selectedImage ? (
                                            <img
                                                className="p-2 rounded"
                                                style={{
                                                    width: "100%",
                                                    objectFit: "contain",
                                                    maxWidth: "400px",
                                                    height: "300px",
                                                }}
                                                src={selectedImage}
                                                alt="image"
                                            />
                                        ) : null}
                                    </div>

                                    {product?.showcase?.length ? (
                                        <div
                                            className="d-flex align-items-center"
                                            style={{ flexWrap: "wrap" }}
                                        >
                                            {product.thumbnail_url ? (
                                                <button className="p-1 rounded mb-1">
                                                    <img
                                                        className="border p-1 rounded"
                                                        onClick={() =>
                                                            setSelectedImage(
                                                                product.thumbnail_url
                                                            )
                                                        }
                                                        src={
                                                            product.thumbnail_url
                                                        }
                                                        width="45px"
                                                        height="45px"
                                                        alt=""
                                                    />
                                                </button>
                                            ) : null}
                                            {product.showcase.map((image) => (
                                                <button
                                                    className="p-1 rounded mb-1"
                                                    key={image.id}
                                                >
                                                    <img
                                                        width="45px"
                                                        height="45px"
                                                        className="border p-1 rounded"
                                                        onClick={() =>
                                                            setSelectedImage(
                                                                image.url
                                                            )
                                                        }
                                                        src={image.url}
                                                        alt=""
                                                    />
                                                </button>
                                            ))}
                                        </div>
                                    ) : null}
                                </div>
                            </div>

                            <div className="w-full lg:w-1/2 py-3 lg:py-0 px-4 lg:px-0">
                                <div>
                                    <div
                                        className="text-gray-400 bold rounded"
                                        style={{ fontSize: "12px" }}
                                    >
                                        <NavLink
                                            href={route(
                                                "reseller.resel-product.index",
                                                {
                                                    cat: product?.category?.id,
                                                }
                                            )}
                                            className="w-full p-1 bg-indigo-700 text-white uppercase"
                                        >
                                            {product?.category?.name ??
                                                "Undefined"}
                                        </NavLink>
                                    </div>
                                    <div className="text-indigo-900 text-bold text-3xl capitalize">
                                        {product?.title ?? ""}
                                    </div>
                                </div>

                                <div className="py-2">
                                    {product?.attr?.name ? (
                                        <>
                                            <hr />
                                            <h4 className="">
                                                {product.attr.name}
                                            </h4>
                                            <div
                                                className="flex justify-start items-center my-1"
                                                style={{
                                                    flexWrap: "wrap",
                                                    gap: "10px",
                                                }}
                                            >
                                                {attrValues.map((attr) => (
                                                    <div
                                                        key={attr}
                                                        className="border rounded mr-2"
                                                        style={{
                                                            width: "45px",
                                                            height: "35px",
                                                            alignContent:
                                                                "center",
                                                            textAlign: "center",
                                                        }}
                                                    >
                                                        {attr}
                                                    </div>
                                                ))}
                                            </div>
                                            <hr />
                                        </>
                                    ) : null}
                                </div>

                                <div className="text-2xl flex bold">
                                    {product?.offer_type ? (
                                        <div className="md:flex items-baseline">
                                            <div
                                                style={{
                                                    fontSize: "22px",
                                                    marginRight: "12px",
                                                }}
                                            >
                                                Price :{" "}
                                                <strong className="text_secondary bold">
                                                    {product.total_price} TK
                                                </strong>
                                            </div>
                                            <div className="flex justify-start items-baseline">
                                                <del
                                                    className="px-1"
                                                    style={{
                                                        fontSize: "22px",
                                                    }}
                                                >
                                                    MRP: {product.price} TK
                                                </del>
                                                <div className="text-xs">
                                                    {discountPercent}% OFF
                                                </div>
                                            </div>
                                        </div>
                                    ) : (
                                        <div
                                            style={{
                                                fontWeight: "bold",
                                                fontSize: "22px",
                                                color: "var(--brand-primary)",
                                                marginRight: "12px",
                                            }}
                                        >
                                            Price : {product?.total_price} TK
                                        </div>
                                    )}
                                </div>

                                <div className="rounded-lg bg-gray-200 p-3 mt-3">
                                    <div className="md:flex justify-between w-full">
                                        <div>
                                            <div>
                                                <div className="text-sm">
                                                    Vendor
                                                </div>
                                                <NavLink className="text-lg text-indigo-900">
                                                    {product?.owner?.name ??
                                                        "n/a"}
                                                </NavLink>
                                            </div>
                                            <Hr />
                                            <div>
                                                <div className="text-sm">
                                                    Shop / Brand
                                                </div>
                                                <div className="text-lg text-indigo-900">
                                                    {product?.owner?.shop
                                                        ?.shop_name_en ?? "n/a"}{" "}
                                                    <span className="text-xs">
                                                        (
                                                        {product?.owner?.shop
                                                            ?.shop_name_bn ??
                                                            "n/a"}
                                                        )
                                                    </span>
                                                    <br />
                                                    <NavLink
                                                        href={route("shops", {
                                                            slug: product?.owner
                                                                ?.shop?.slug,
                                                            id: product?.owner
                                                                ?.shop?.id,
                                                        })}
                                                    >
                                                        visit shops{" "}
                                                        <i className="fas fa-angle-right pl-2"></i>
                                                    </NavLink>
                                                </div>
                                            </div>
                                            <Hr />
                                            <div>
                                                <div className="text-sm">
                                                    Addrss
                                                </div>
                                                <div className="text-sm text-indigo-900">
                                                    {product?.owner?.address ??
                                                        "n/a"}
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <div>
                                                <div className="text-sm">
                                                    Phone
                                                </div>
                                                <div className="text-lg text-indigo-900">
                                                    {product?.owner?.phone ??
                                                        "n/a"}
                                                </div>
                                            </div>
                                            <Hr />
                                            <div>
                                                <div className="text-sm">
                                                    Email
                                                </div>
                                                <div className="text-lg text-indigo-900">
                                                    {product?.owner?.email ??
                                                        "n/a"}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </SectionInner>
                </Section>

                <Section>
                    <SectionInner>
                        <div className="font-bold">Description</div>
                        <div
                            dangerouslySetInnerHTML={{
                                __html: product?.description ?? "",
                            }}
                        />
                    </SectionInner>
                </Section>

                <div style={{ width: "170px" }} className="shadow-xl">
                    <ReselProductCart product={product} />
                </div>
                <Hr />
            </Container>

            <Modal show={showConfirm} onClose={() => setShowConfirm(false)}>
                <div className="p-2 px-4">
                    <div className="py-2 font-bold">Resel Product</div>
                    <Hr />
                    <div className="text-sm">
                        Your are going to add this product to your product list
                        to resell this with a veiw to earn more profit with your
                        custom price. Your may able to update product price
                        after product successfully cloed to your product list.
                        By clicking <strong>CONFIRM</strong> button, bellow task
                        goig to be done ...
                        <Hr />
                        <ul list-item="number">
                            <li>
                                <i className="fas fa-check-circle w-6 pr-2"></i>{" "}
                                Product going to be add to your product list.
                            </li>
                            <li>
                                <i className="fas fa-check-circle w-6 pr-2"></i>{" "}
                                Sytem take a track for your reseling.
                            </li>
                            <li>
                                <i className="fas fa-check-circle w-6 pr-2"></i>{" "}
                                Product owner get a message from you that you
                                are reselling this products.
                            </li>
                        </ul>
                    </div>
                    <Hr />
                    <div>
                        <div className="p-3 mb-2 bg-gray-100">
                            <div className="mb-1">
                                {product?.thumbnail_url ? (
                                    <img
                                        src={product.thumbnail_url}
                                        className="h-12 w-12 rounded-md mb-2"
                                        alt=""
                                    />
                                ) : null}

                                <p className="text-lg">{product?.title}</p>

                                {product?.offer_type ? (
                                    <div className="md:flex items-baseline">
                                        <div className="font-bold font-normal text-md">
                                            Price :{" "}
                                            <strong>
                                                {product?.total_price} TK
                                            </strong>
                                        </div>
                                        <div className="flex justify-start items-baseline">
                                            <del
                                                className="px-1"
                                                style={{ fontSize: "14px" }}
                                            >
                                                MRP: {product?.price} TK
                                            </del>
                                            <div className="text-xs">
                                                {discountPercent}% OFF
                                            </div>
                                        </div>
                                    </div>
                                ) : (
                                    <div
                                        style={{
                                            fontWeight: "bold",
                                            fontSize: "18px",
                                            color: "var(--brand-primary)",
                                            marginRight: "12px",
                                        }}
                                    >
                                        Price : {product?.total_price} TK
                                    </div>
                                )}
                            </div>
                        </div>
                        <div>
                            <label className="text-sm font-bold">
                                Resel Price
                            </label>
                            <input
                                type="number"
                                min={product?.price ?? 0}
                                className="w-full rounded"
                                value={form.data.resel_price}
                                onChange={(e) =>
                                    form.setData(
                                        "resel_price",
                                        e.target.value
                                    )
                                }
                            />
                            {form.errors.resel_price ? (
                                <p className="text-red-400">
                                    {form.errors.resel_price}
                                </p>
                            ) : null}
                        </div>
                        <div className="mt-2 border rounded p-2 shadow">
                            <div className="flex justify-between mb-2">
                                <label className="text-sm font-bold">
                                    Resel Discount Price
                                </label>
                                <input
                                    type="checkbox"
                                    value="true"
                                    checked={
                                        form.data.is_resel_with_discount_price
                                    }
                                    onChange={(e) =>
                                        form.setData(
                                            "is_resel_with_discount_price",
                                            e.target.checked
                                        )
                                    }
                                    style={{ width: "20px", height: "20px" }}
                                />
                            </div>
                            {form.data.is_resel_with_discount_price ? (
                                <div>
                                    <input
                                        placeholder="Discount Price"
                                        min={product?.price ?? 0}
                                        type="number"
                                        className="w-full rounded"
                                        value={form.data.resel_discount_price}
                                        onChange={(e) =>
                                            form.setData(
                                                "resel_discount_price",
                                                e.target.value
                                            )
                                        }
                                    />
                                    {form.errors.resel_discount_price ? (
                                        <p className="text-red-400">
                                            {form.errors.resel_discount_price}
                                        </p>
                                    ) : null}
                                </div>
                            ) : null}
                        </div>
                        <div className="mt-2">
                            Profit :
                            {form.data.is_resel_with_discount_price ? (
                                <>
                                    <span className="text-red-500 px-2">
                                        (with discount)
                                    </span>
                                    <span className="text-red-500">
                                        {profit}
                                    </span>
                                </>
                            ) : (
                                <>
                                    <span className="text-red-500 px-2">
                                        (without discount)
                                    </span>
                                    <span className="text-red-500">
                                        {profit}
                                    </span>
                                </>
                            )}
                        </div>
                        <Hr />
                        <div>
                            <label className="text-sm font-bold">
                                Reseller Category
                            </label>
                            <select
                                className="rounded border w-full"
                                value={form.data.reseller_category_id}
                                onChange={(e) =>
                                    form.setData(
                                        "reseller_category_id",
                                        e.target.value
                                    )
                                }
                            >
                                <option value="">Select Category</option>
                                {renderCategoryOptions(categories)}
                            </select>
                            {form.errors.reseller_category_id ? (
                                <p className="text-red-900">
                                    {form.errors.reseller_category_id}
                                </p>
                            ) : null}
                        </div>
                    </div>
                    <Hr />
                    For procced, click to confirm button. After successfully
                    cloned, You can update resel product from your product list.
                    <div className="flex justify-end items-start p-2">
                        <PrimaryButton
                            type="button"
                            onClick={confirmClone}
                        >
                            <i className="fas fa-sync pr-2"></i> Confirm
                        </PrimaryButton>
                    </div>
                </div>
            </Modal>
        </AppLayout>
    );
}
