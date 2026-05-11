import { Head, useForm } from "@inertiajs/react";
import { useMemo, useRef, useState } from "react";
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
    const [showVideoModal, setShowVideoModal] = useState(false);
    const [isZooming, setIsZooming] = useState(false);
    const [lensPosition, setLensPosition] = useState({ x: 0, y: 0 });
    const [bgPosition, setBgPosition] = useState("0px 0px");
    const imageRef = useRef(null);

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

    const gallery = useMemo(() => {
        const items = [];

        if (product?.thumbnail_url) {
            items.push({
                type: "image",
                key: `thumb-${product.thumbnail_url}`,
                value: product.thumbnail_url,
            });
        }

        (product?.showcase ?? []).forEach((image) => {
            if (image?.url) {
                items.push({
                    type: "image",
                    key: `showcase-${image.id}`,
                    value: image.url,
                });
            }
        });

        if (product?.video_url) {
            items.push({
                type: "video",
                key: `video-${product.video_url}`,
                value: product.video_url,
            });
        }

        return items.filter(
            (item, index, array) =>
                array.findIndex(
                    (candidate) =>
                        candidate.type === item.type &&
                        candidate.value === item.value,
                ) === index,
        );
    }, [product]);

    const handleMouseMove = (event) => {
        const image = imageRef.current;

        if (!image) return;

        const rect = image.getBoundingClientRect();
        const lensWidth = 120;
        const lensHeight = 120;
        const zoom = 4;

        let x = event.clientX - rect.left - lensWidth / 2;
        let y = event.clientY - rect.top - lensHeight / 2;

        x = Math.max(0, Math.min(x, rect.width - lensWidth));
        y = Math.max(0, Math.min(y, rect.height - lensHeight));

        setLensPosition({ x, y });
        setBgPosition(`-${x * zoom}px -${y * zoom}px`);
    };

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
            <style>{`
                .resel-product-zoom {
                    display: flex;
                    gap: 20px;
                    align-items: flex-start;
                }

                .resel-product-image-area {
                    position: relative;
                    width: 360px;
                    border: 1px solid #eee;
                    background: #fff;
                    flex-shrink: 0;
                }

                @media (max-width: 1024px) {
                    .resel-product-zoom {
                        display: block;
                    }

                    .resel-product-image-area {
                        width: 100%;
                    }
                }
            `}</style>

            <Container>
                <Section>
                    <SectionHeader
                        title={
                            <div className="items-center justify-between md:flex">
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
                                        <div className="p-2 text-red-800 bg-red-200">
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
                                            <i className="pr-2 fas fa-sync"></i>{" "}
                                            resell
                                        </PrimaryButton>
                                    )}
                                </div>
                            </div>
                        }
                    />
                    <Hr />

                    <SectionInner>
                        <div className="items-start gap-6 p-2 lg:flex">
                            <div className="w-full lg:max-w-[460px] xl:max-w-[500px]">
                                <div className="resel-product-zoom">
                                    <div
                                        className="resel-product-image-area shrink-0"
                                        onMouseEnter={() =>
                                            setIsZooming(true)
                                        }
                                        onMouseLeave={() =>
                                            setIsZooming(false)
                                        }
                                        onMouseMove={handleMouseMove}
                                    >
                                        {selectedImage ? (
                                            <img
                                                ref={imageRef}
                                                className="p-2 rounded"
                                                style={{
                                                    width: "100%",
                                                    objectFit: "contain",
                                                    maxWidth: "360px",
                                                    height: "360px",
                                                }}
                                                src={selectedImage}
                                                alt="image"
                                            />
                                        ) : null}

                                        {isZooming ? (
                                            <div
                                                style={{
                                                    position: "absolute",
                                                    width: "120px",
                                                    height: "120px",
                                                    border: "2px solid #ff6a00",
                                                    background:
                                                        "rgba(255, 255, 255, 0.35)",
                                                    left: `${lensPosition.x}px`,
                                                    top: `${lensPosition.y}px`,
                                                    pointerEvents: "none",
                                                    zIndex: 10,
                                                }}
                                            />
                                        ) : null}
                                    </div>

                                    {gallery.length > 1 ? (
                                        <div className="flex flex-wrap items-center gap-1 md:block">
                                            {gallery.map((item) => (
                                                <button
                                                    type="button"
                                                    className="p-1 mb-1 rounded"
                                                    key={item.key}
                                                    onClick={() => {
                                                        if (
                                                            item.type ===
                                                            "video"
                                                        ) {
                                                            setShowVideoModal(
                                                                true,
                                                            );
                                                            return;
                                                        }

                                                        setSelectedImage(
                                                            item.value,
                                                        );
                                                    }}
                                                >
                                                    {item.type === "video" ? (
                                                        <div
                                                            className="relative flex items-center justify-center p-1 border rounded bg-slate-900"
                                                            style={{
                                                                width: "60px",
                                                                height: "60px",
                                                            }}
                                                        >
                                                            <video
                                                                src={item.value}
                                                                muted
                                                                className="absolute inset-0 object-cover w-full h-full rounded opacity-70"
                                                            />
                                                            <span className="relative z-10 flex items-center justify-center w-8 h-8 text-white rounded-full bg-black/60">
                                                                <i className="text-xs fas fa-play"></i>
                                                            </span>
                                                        </div>
                                                    ) : (
                                                        <img
                                                            width="60"
                                                            height="60"
                                                            className="p-1 border rounded"
                                                            src={item.value}
                                                            alt=""
                                                        />
                                                    )}
                                                </button>
                                            ))}
                                        </div>
                                    ) : null}
                                </div>
                            </div>

                            <div className="relative flex-1 w-full min-w-0 px-4 py-3 lg:px-0 lg:py-0">
                                <div
                                    style={{
                                        position: "absolute",
                                        top: "0",
                                        right: "0",
                                        width: "100%",
                                        height: "420px",
                                        border: "1px solid #eee",
                                        backgroundRepeat: "no-repeat",
                                        backgroundColor: "#fff",
                                        display:
                                            isZooming &&
                                            typeof window !== "undefined" &&
                                            window.innerWidth > 1024
                                                ? "block"
                                                : "none",
                                        zIndex: 30,
                                        boxShadow:
                                            "0 8px 24px rgba(0,0,0,.15)",
                                        backgroundImage: selectedImage
                                            ? `url('${selectedImage}')`
                                            : "none",
                                        backgroundSize: "1680px 1680px",
                                        backgroundPosition: bgPosition,
                                    }}
                                />
                                <div>
                                    <div
                                        className="text-gray-400 rounded bold"
                                        style={{ fontSize: "12px" }}
                                    >
                                        <NavLink
                                            href={route(
                                                "reseller.resel-product.index",
                                                {
                                                    cat: product?.category?.id,
                                                }
                                            )}
                                            className="w-full p-1 text-white uppercase bg-indigo-700 hover:text-white"
                                        >
                                            {product?.category?.name ??
                                                "Undefined"}
                                        </NavLink>
                                    </div>
                                    <div className="text-3xl text-indigo-900 capitalize text-bold">
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
                                                className="flex items-center justify-start my-1"
                                                style={{
                                                    flexWrap: "wrap",
                                                    gap: "10px",
                                                }}
                                            >
                                                {attrValues.map((attr) => (
                                                    <div
                                                        key={attr}
                                                        className="mr-2 border rounded"
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

                                <div className="flex text-2xl bold">
                                    {product?.offer_type ? (
                                        <div className="items-baseline md:flex">
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
                                            <div className="flex items-baseline justify-start">
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

                                <div className="p-3 mt-3 bg-gray-200 rounded-lg">
                                    <div className="justify-between w-full md:flex">
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
                                                        <i className="pl-2 fas fa-angle-right"></i>
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
                                <i className="w-6 pr-2 fas fa-check-circle"></i>{" "}
                                Product going to be add to your product list.
                            </li>
                            <li>
                                <i className="w-6 pr-2 fas fa-check-circle"></i>{" "}
                                Sytem take a track for your reseling.
                            </li>
                            <li>
                                <i className="w-6 pr-2 fas fa-check-circle"></i>{" "}
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
                                        className="w-12 h-12 mb-2 rounded-md"
                                        alt=""
                                    />
                                ) : null}

                                <p className="text-lg">{product?.title}</p>

                                {product?.offer_type ? (
                                    <div className="items-baseline md:flex">
                                        <div className="font-normal font-bold text-md">
                                            Price :{" "}
                                            <strong>
                                                {product?.total_price} TK
                                            </strong>
                                        </div>
                                        <div className="flex items-baseline justify-start">
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
                        <div className="p-2 mt-2 border rounded shadow">
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
                                    <span className="px-2 text-red-500">
                                        (with discount)
                                    </span>
                                    <span className="text-red-500">
                                        {profit}
                                    </span>
                                </>
                            ) : (
                                <>
                                    <span className="px-2 text-red-500">
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
                                className="w-full border rounded"
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
                    <div className="flex items-start justify-end p-2">
                        <PrimaryButton
                            type="button"
                            onClick={confirmClone}
                        >
                            <i className="pr-2 fas fa-sync"></i> Confirm
                        </PrimaryButton>
                    </div>
                </div>
            </Modal>

            {showVideoModal && product?.video_url ? (
                <div
                    className="fixed inset-0 z-[10000] flex items-center justify-center p-4 bg-black/70"
                    onClick={() => setShowVideoModal(false)}
                >
                    <div
                        className="relative w-full max-w-4xl p-3 bg-white rounded-lg shadow-2xl"
                        onClick={(event) => event.stopPropagation()}
                    >
                        <button
                            type="button"
                            onClick={() => setShowVideoModal(false)}
                            className="absolute flex items-center justify-center w-10 h-10 text-white rounded-full top-3 right-3 bg-black/70 hover:bg-black"
                        >
                            <i className="fas fa-times"></i>
                        </button>

                        <video
                            key={product.video_url}
                            src={product.video_url}
                            controls
                            autoPlay
                            className="w-full rounded-lg max-h-[80vh] bg-black"
                        />
                    </div>
                </div>
            ) : null}
        </AppLayout>
    );
}
