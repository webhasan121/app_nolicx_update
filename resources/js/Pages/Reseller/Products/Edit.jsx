import { router, useForm, usePage, Head } from "@inertiajs/react";
import { useEffect, useId, useRef, useState } from "react";
import AppLayout from "../../../Layouts/App";
import Hr from "../../../components/Hr";
import Image from "../../../components/Image";
import InputField from "../../../components/InputField";
import InputFile from "../../../components/InputFile";
import NavLink from "../../../components/NavLink";
import PrimaryButton from "../../../components/PrimaryButton";
import ProductAttributesInput from "../../../components/ProductAttributesInput";
import CategorySelect from "../../../components/CategorySelect";
import SecondaryButton from "../../../components/SecondaryButton";
import PageHeader from "../../../components/dashboard/PageHeader";
import Container from "../../../components/dashboard/Container";
import SectionHeader from "../../../components/dashboard/section/Header";
import SectionInner from "../../../components/dashboard/section/Inner";
import SectionSection from "../../../components/dashboard/section/Section";
import {
    PRODUCT_VIDEO_DURATION_ERROR,
    validateProductVideoDuration,
} from "../../../utils/videoValidation";
import useTranslation from "../../../hooks/useTranslation";

const MAX_OTHER_IMAGES = 8;

function ProductNavigations({ productId, nav = "Product", t }) {
    return (
        <div className="flex ">
            <NavLink
                href={route("vendor.products.edit", {
                    product: productId,
                    nav: "Product",
                })}
                active={nav === "Product"}
            >{t("Product")}</NavLink>

            <div>
                <NavLink
                    href={route("vendor.products.resell", { product: productId })}
                    active={nav === "Resell"}
                >{t("Resell")}</NavLink>
            </div>
        </div>
    );
}

export default function Edit() {
    const { t } = useTranslation();
    const { productData, categories = [], errors = {}, auth } = usePage().props;
    const inputId = useId().replace(/:/g, "");
    const editorRef = useRef(null);
    const [trixReady, setTrixReady] = useState(
        typeof window !== "undefined" && !!window.Trix
    );
    console.log("productData", productData);

    const form = useForm({
        name: productData?.name ?? "",
        title: productData?.title ?? "",
        category_id: productData?.category_id ?? "",
        buying_price: productData?.buying_price ?? "",
        price: productData?.price ?? "",
        discount: productData?.discount ?? "",
        offer_type: !!productData?.offer_type,
        display_at_home: !!productData?.display_at_home,
        unit: productData?.unit ?? "",
        description: productData?.description ?? "",
        meta_title: productData?.meta_title ?? "",
        meta_description: productData?.meta_description ?? "",
        keyword: productData?.keyword ?? "",
        meta_tags: productData?.meta_tags ?? "",
        cod: !!productData?.cod,
        courier: !!productData?.courier,
        hand: !!productData?.hand,
        shipping_in_dhaka: productData?.shipping_in_dhaka ?? "",
        shipping_out_dhaka: productData?.shipping_out_dhaka ?? "",
        shipping_note: productData?.shipping_note ?? "",
        attr_name: productData?.attr?.name ?? "",
        attr_value: productData?.attr?.value ?? "",
        attributes: productData?.attrs?.length
            ? productData.attrs
            : [{ name: productData?.attr?.name ?? "", value: productData?.attr?.value ?? "" }],
        thumb: null,
        video: productData?.video ?? "",
        newseothumb: null,
        newImage: [],
    });
    const existingOtherImageCount = productData?.related_images?.length ?? 0;
    const selectedOtherImageCount = Array.isArray(form.data.newImage)
        ? form.data.newImage.length
        : 0;
    const remainingOtherImageSlots = Math.max(
        0,
        MAX_OTHER_IMAGES - existingOtherImageCount - selectedOtherImageCount
    );

    useEffect(() => {
        let isMounted = true;

        if (typeof window === "undefined" || window.Trix) {
            setTrixReady(true);
            return undefined;
        }

        if (!document.querySelector('link[data-trix="true"]')) {
            const link = document.createElement("link");
            link.rel = "stylesheet";
            link.type = "text/css";
            link.href = "https://unpkg.com/trix@2.0.8/dist/trix.css";
            link.dataset.trix = "true";
            document.head.appendChild(link);
        }

        let script = document.querySelector('script[data-trix="true"]');
        const onLoad = () => {
            if (isMounted) {
                setTrixReady(true);
            }
        };

        if (!script) {
            script = document.createElement("script");
            script.src = "https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js";
            script.async = true;
            script.dataset.trix = "true";
            script.addEventListener("load", onLoad);
            document.body.appendChild(script);
        } else if (window.Trix) {
            setTrixReady(true);
        } else {
            script.addEventListener("load", onLoad);
        }

        return () => {
            isMounted = false;
            if (script) {
                script.removeEventListener("load", onLoad);
            }
        };
    }, []);

    useEffect(() => {
        const editor = editorRef.current;

        if (!editor || !trixReady) {
            return undefined;
        }

        const handleChange = (event) => {
            form.setData("description", event.target.value);
        };

        editor.addEventListener("trix-change", handleChange);

        if (form.data.description && editor.editor) {
            editor.editor.loadHTML(form.data.description);
        }

        return () => {
            editor.removeEventListener("trix-change", handleChange);
        };
    }, [trixReady]);

    const save = (e) => {
        e.preventDefault();
        form.post(route("reseller.products.update", { id: productData.encrypted_id }), {
            forceFormData: true,
        });
    };

    const handleOtherImagesChange = (event) => {
        const currentFiles = Array.isArray(form.data.newImage)
            ? form.data.newImage
            : [];
        const selectedFiles = Array.from(event.target.files ?? []);

        if (selectedFiles.length > remainingOtherImageSlots) {
            window.alert(`You can upload ${remainingOtherImageSlots} more other image(s).`);
            event.target.value = "";
            return;
        }

        form.setData("newImage", [...currentFiles, ...selectedFiles]);
        event.target.value = "";
    };

    const moveToTrash = () => {
        if (!window.confirm("Are you sure you want to move this product to trash?")) {
            return;
        }

        router.post(route("reseller.products.trash", { id: productData.encrypted_id }));
    };

    const restoreFromTrash = () => {
        router.post(route("reseller.products.restore", { id: productData.encrypted_id }));
    };

    const eraseOldImage = (imageId) => {
        if (!window.confirm("Are you sure you want to delete this image?")) {
            return;
        }

        router.delete(
            route("reseller.products.images.destroy", {
                id: productData.encrypted_id,
                image: imageId,
            })
        );
    };

    const hideUnit =
        productData?.is_resel && productData?.user_id === auth?.user?.id;

    return (
        <AppLayout
            title={t("Product Edit")}
            header={
                <PageHeader>{t("Product Edit")}<br />
                    <ProductNavigations productId={productData.encrypted_id} t={t} />
                </PageHeader>
            }
        >
            <Head title={t("Product Edit")} />
            <Container>
                <SectionSection>
                    <SectionHeader
                        title={
                            <div className="flex justify-between text-xs">
                                <div>
                                    {productData.deleted_at ? (
                                        <div
                                            style={{
                                                color: "red",
                                                fontWeight: "bolder",
                                            }}
                                        >{t("Trashed")}</div>
                                    ) : (
                                        <div>
                                            {productData.status ? "Active" : "Drafted"}{" "}
                                            | {productData.created_at_human}
                                        </div>
                                    )}
                                </div>
                                <div>
                                    {productData.deleted_at ? (
                                        <SecondaryButton
                                            type="button"
                                            onClick={restoreFromTrash}
                                        >
                                            <i className="mr-2 fa-solid fa-sync"></i>{" "}{t("Restore")}</SecondaryButton>
                                    ) : (
                                        <SecondaryButton
                                            type="button"
                                            onClick={moveToTrash}
                                        >
                                            <i className="mr-2 fa-solid fa-trash"></i>{" "}{t("Trash")}</SecondaryButton>
                                    )}
                                </div>
                            </div>
                        }
                        content={
                            <div className="flex justify-between">
                                <div>
                                    <div>
                                        {productData.thumbnail_url ? (
                                            <Image src={productData.thumbnail_url} />
                                        ) : null}
                                    </div>
                                    <div>{productData.title ?? "N/A"}</div>

                                    <div className="text-sm">{t("Category :")}{" "}
                                        <strong>
                                            {productData.category_name ?? "N/A"}
                                        </strong>
                                    </div>
                                </div>
                                <div>
                                    <div className="text-sm">{t("Type :")}{productData.is_resel ? (
                                            <span className="px-2 text-white bg-indigo-900 rounded-lg text-md">{t("Resel")}</span>
                                        ) : (
                                            <span className="px-2 text-white bg-indigo-900 rounded-lg text-md">{t("Owner")}</span>
                                        )}
                                    </div>
                                </div>
                            </div>
                        }
                    />
                </SectionSection>

                <form onSubmit={save}>
                    <div className="md:flex md:gap-4">
                        <SectionSection className="md:flex-1">
                            <SectionHeader title={t("Product Basic Info")} content="" />
                            <SectionInner>
                                <InputField
                                    error={errors.name}
                                    labelWidth="350px"
                                    label={t("Products Name")}
                                    name="name"
                                    inputClass="w-full"
                                    value={form.data.name}
                                    onChange={(e) =>
                                        form.setData("name", e.target.value)
                                    }
                                />
                                <InputFile
                                    labelWidth="200px"
                                    error="title"
                                    label={t("Products title")}
                                    name="title"
                                    errors={errors}
                                >
                                    <textarea
                                        rows="3"
                                        id=""
                                        className="w-full rounded"
                                        value={form.data.title}
                                        onChange={(e) =>
                                            form.setData("title", e.target.value)
                                        }
                                    ></textarea>
                                </InputFile>

                                <Hr />
                                <InputFile
                                    labelWidth="250px"
                                    label={t("Products Category")}
                                    error="category_id"
                                    errors={errors}
                                >
                                    <div className="text-xs">{t("Category :")}{" "}
                                        <strong>
                                            {productData.category_name ?? "N/A"}
                                        </strong>{t(". Change to another")}</div>
                                    <CategorySelect
                                        categories={categories}
                                        value={form.data.category_id ?? ""}
                                        onChange={(categoryId) =>
                                            form.setData(
                                                "category_id",
                                                categoryId
                                            )
                                        }
                                        placeholder={t("-- Select Category --")}
                                        noneLabel={t("-- Select Category --")}
                                        noResultsLabel={t("No category found.")}
                                    />
                                </InputFile>
                                <Hr />
                            </SectionInner>
                        </SectionSection>

                        <SectionSection className="md:w-[324px] md:flex-none">
                            <SectionHeader title={t("Product Price")} content="" />
                            <SectionInner>
                                <div>
                                    <InputField
                                        className="mx-1 "
                                        labelWidth="100px"
                                        label={t("Product Buying Price")}
                                        name="buying_price"
                                        error={errors.buying_price}
                                        value={form.data.buying_price}
                                        onChange={(e) =>
                                            form.setData(
                                                "buying_price",
                                                e.target.value
                                            )
                                        }
                                    />
                                    <InputField
                                        className="mx-1 "
                                        labelWidth="100px"
                                        label={t("Product Sell Price")}
                                        name="price"
                                        error={errors.price}
                                        value={form.data.price}
                                        onChange={(e) =>
                                            form.setData("price", e.target.value)
                                        }
                                    />
                                    <InputField
                                        className={`${hideUnit ? "hidden disabled" : ""} mx-1`}
                                        labelWidth="100px"
                                        type="number"
                                        label={t("Product Unite")}
                                        name="unit"
                                        error={errors.unit}
                                        value={form.data.unit}
                                        onChange={(e) =>
                                            form.setData("unit", e.target.value)
                                        }
                                    />
                                </div>
                                <Hr />
                                <div>
                                    <InputFile
                                        label={t("Wish to sell with Discount")}
                                        name="offer_type"
                                        error="offer_type"
                                        errors={errors}
                                    >
                                        <input
                                            type="checkbox"
                                            checked={form.data.offer_type}
                                            onChange={(e) =>
                                                form.setData(
                                                    "offer_type",
                                                    e.target.checked
                                                )
                                            }
                                            style={{
                                                width: "25px",
                                                height: "25px",
                                            }}
                                        />
                                    </InputFile>
                                    {form.data.offer_type ? (
                                        <InputField
                                            labelWidth="250px"
                                            label={t("Product Discount Price")}
                                            name="discount"
                                            error={errors.discount}
                                            value={form.data.discount}
                                            onChange={(e) =>
                                                form.setData(
                                                    "discount",
                                                    e.target.value
                                                )
                                            }
                                        />
                                    ) : null}
                                </div>
                                <Hr />
                                <div>
                                    <InputFile
                                        label={t("Set to Recomended Products")}
                                        name="display_at_home"
                                        error="display_at_home"
                                        errors={errors}
                                    >
                                        <input
                                            type="checkbox"
                                            checked={
                                                form.data.display_at_home
                                            }
                                            onChange={(e) =>
                                                form.setData(
                                                    "display_at_home",
                                                    e.target.checked
                                                )
                                            }
                                            style={{
                                                width: "25px",
                                                height: "25px",
                                            }}
                                        />
                                    </InputFile>
                                </div>
                            </SectionInner>
                        </SectionSection>
                    </div>

                    <SectionSection>
                        <SectionHeader
                            title={t("Product Delevery")}
                            content={t("Define your product delevery option and charge from here.")}
                        />
                        <SectionInner>
                            <div className="justify-between md:flex ">
                                <div>
                                    <InputFile
                                        error="cod"
                                        label={t("Available Cash-On-Delevery")}
                                        name="cod"
                                        errors={errors}
                                    >
                                        <input
                                            checked={form.data.cod}
                                            onChange={(e) =>
                                                form.setData(
                                                    "cod",
                                                    e.target.checked
                                                )
                                            }
                                            type="checkbox"
                                            style={{
                                                width: "25px",
                                                height: "25px",
                                            }}
                                        />
                                    </InputFile>
                                    <Hr />
                                    <InputFile
                                        error="courier"
                                        label={t("Available Couried Delivery")}
                                        name="courier"
                                        errors={errors}
                                    >
                                        <input
                                            checked={form.data.courier}
                                            onChange={(e) =>
                                                form.setData(
                                                    "courier",
                                                    e.target.checked
                                                )
                                            }
                                            type="checkbox"
                                            style={{
                                                width: "25px",
                                                height: "25px",
                                            }}
                                        />
                                    </InputFile>
                                    <Hr />
                                    <InputFile
                                        error="hand"
                                        label={t("Available Hand-To-Hand Delevery")}
                                        name="hand"
                                        errors={errors}
                                    >
                                        <input
                                            checked={form.data.hand}
                                            onChange={(e) =>
                                                form.setData(
                                                    "hand",
                                                    e.target.checked
                                                )
                                            }
                                            type="checkbox"
                                            style={{
                                                width: "25px",
                                                height: "25px",
                                            }}
                                        />
                                    </InputFile>
                                </div>
                                <div>
                                    <InputField
                                        label={t("Delevery Amount Inside Dhaka")}
                                        name="shipping_in_dhaka"
                                        className="lg:flex"
                                        labelWidth="250px"
                                        error={errors.shipping_in_dhaka}
                                        value={form.data.shipping_in_dhaka}
                                        onChange={(e) =>
                                            form.setData(
                                                "shipping_in_dhaka",
                                                e.target.value
                                            )
                                        }
                                    />
                                    <Hr />
                                    <InputField
                                        label={t("Normal Delevery Amount")}
                                        className="lg:flex"
                                        name="shipping_out_dhaka"
                                        labelWidth="250px"
                                        error={errors.shipping_out_dhaka}
                                        value={form.data.shipping_out_dhaka}
                                        onChange={(e) =>
                                            form.setData(
                                                "shipping_out_dhaka",
                                                e.target.value
                                            )
                                        }
                                    />
                                    <Hr />
                                    <InputFile
                                        label={t("Shipping Note")}
                                        error="shipping_note"
                                        name="shipping_note"
                                        labelWidth="250px"
                                        errors={errors}
                                    >
                                        <textarea
                                            id="psn"
                                            rows="3"
                                            className="w-full rounded"
                                            placeholder={t("write your shipping note ...")}
                                            value={form.data.shipping_note}
                                            onChange={(e) =>
                                                form.setData(
                                                    "shipping_note",
                                                    e.target.value
                                                )
                                            }
                                        ></textarea>
                                    </InputFile>
                                </div>
                            </div>
                        </SectionInner>
                    </SectionSection>

                    <SectionSection>
                        <SectionHeader
                            title={t("SEO")}
                            content={t("Setup your product seo from here.")}
                        />
                        <SectionInner>
                            <InputField
                                error={errors.keyword}
                                label={t("Meta Keyword")}
                                name="keyword"
                                className="lg:flex"
                                inputClass="w-full"
                                value={form.data.keyword}
                                onChange={(e) =>
                                    form.setData("keyword", e.target.value)
                                }
                            />
                            <InputField
                                error={errors.meta_title}
                                label={t("Meta Title")}
                                name="meta_title"
                                className="lg:flex"
                                inputClass="w-full"
                                value={form.data.meta_title}
                                onChange={(e) =>
                                    form.setData("meta_title", e.target.value)
                                }
                            />
                            <InputField
                                error={errors.meta_tags}
                                label={t("Meta Tags")}
                                name="meta_tags"
                                className="lg:flex"
                                inputClass="w-full"
                                value={form.data.meta_tags}
                                onChange={(e) =>
                                    form.setData("meta_tags", e.target.value)
                                }
                            />
                            <InputFile
                                error="meta_description"
                                label={t("Meta Description")}
                                name="meta_description"
                                errors={errors}
                            >
                                <textarea
                                    className="w-full p-2 rounded-md shadow"
                                    rows="4"
                                    placeholder={t("Meta Description ....")}
                                    value={form.data.meta_description}
                                    onChange={(e) =>
                                        form.setData(
                                            "meta_description",
                                            e.target.value
                                        )
                                    }
                                ></textarea>
                            </InputFile>
                            <InputFile
                                error="newseothumb"
                                label={t("Meta Thumbnail")}
                                name="thumbnail"
                                errors={errors}
                            >
                                <div>
                                    {form.data.newseothumb ? (
                                        <img
                                            src={URL.createObjectURL(
                                                form.data.newseothumb
                                            )}
                                            width="100px"
                                            height="200px"
                                            alt=""
                                        />
                                    ) : productData.meta_thumbnail_url ? (
                                        <img
                                            src={productData.meta_thumbnail_url}
                                            width="100px"
                                            height="200px"
                                            alt=""
                                        />
                                    ) : null}
                                </div>
                                <div className="relative">
                                    <p>{t("100 x 200 meta thumbnail")}</p>
                                    <input
                                        type="file"
                                        id="newseothumb"
                                        className="absolute hidden"
                                        onChange={(e) =>
                                            form.setData(
                                                "newseothumb",
                                                e.target.files[0] ?? null
                                            )
                                        }
                                    />
                                    <label htmlFor="newseothumb">
                                        <i className="px-2 fas fa-upload"></i>
                                    </label>
                                </div>
                            </InputFile>
                        </SectionInner>
                    </SectionSection>

                    <SectionSection>
                        <SectionHeader
                            title={t("Image Attributes")}
                            content={t("Give your products attributes, product different types, different product color package and quantity.")}
                        />
                        <SectionInner>
                            <ProductAttributesInput
                                attributes={form.data.attributes}
                                onChange={(attributes) => form.setData("attributes", attributes)}
                            />
                        </SectionInner>
                    </SectionSection>

                    <SectionSection>
                        <div className="justify-between md:flex flex-rowreverse">
                            <SectionHeader
                                title={t("Image Thumbnail")}
                                content={
                                    <div>{t("Provide a mendatory thumbnail image for your products. This image consider for the thumbnail for social media platform.")}<div className="relative mt-3">
                                            <p className="mb-2 text-xs">{t("600 x 600 image thumbnail")}</p>
                                            <input
                                                id="prod_thumb"
                                                type="file"
                                                className="absolute hidden p-1 border"
                                                onChange={(e) =>
                                                    form.setData(
                                                        "thumb",
                                                        e.target.files[0] ?? null
                                                    )
                                                }
                                            />
                                            <label
                                                htmlFor="prod_thumb"
                                                className="inline-flex items-center justify-center border rounded cursor-pointer w-9 h-9"
                                            >
                                                <i className="fas fa-upload"></i>
                                            </label>
                                        </div>
                                    </div>
                                }
                            />

                            <SectionInner>
                                {productData.thumbnail_url && !form.data.thumb ? (
                                    <Image src={productData.thumbnail_url} />
                                ) : null}
                                {form.data.thumb ? (
                                    <img
                                        src={URL.createObjectURL(form.data.thumb)}
                                        width="100px"
                                        height="200px"
                                        alt=""
                                    />
                                ) : null}
                            </SectionInner>
                        </div>
                    </SectionSection>

                    <SectionSection>
                        <SectionHeader
                            title={t("Product Video")}
                            content={t("Add an optional YouTube video URL for the details page.")}
                        />
                        <SectionInner>
                            <InputField
                                label={t("YouTube URL")}
                                name="video"
                                value={form.data.video}
                                onChange={(e) => form.setData("video", e.target.value)}
                                error={errors.video}
                            />
                        </SectionInner>
                    </SectionSection>

                    <SectionSection>
                        <SectionHeader
                            title={t("Other Image")}
                            content={t("Other product image that showcase your product. other image mainly display at product details page.")}
                        />

                        <SectionInner>
                            <div
                                style={{
                                    display: "grid",
                                    gridTemplateColumns:
                                        "repeat(auto-fit,100px)",
                                    gridGap: "10px",
                                }}
                            >
                                {(productData.related_images ?? []).map((item) => (
                                    <div className="p-2 border" key={item.id}>
                                        <Image src={item.url} />
                                        <button
                                            type="button"
                                            onClick={() =>
                                                eraseOldImage(item.id)
                                            }
                                        >{t("Erage")}</button>
                                    </div>
                                ))}
                            </div>

                            <Hr />
                            <div
                                style={{
                                    display: "grid",
                                    gridTemplateColumns:
                                        "repeat(auto-fit,50px)",
                                    gridGap: "10px",
                                }}
                            >
                                {Array.from(form.data.newImage ?? []).map(
                                    (ni, index) => (
                                        <div
                                            className="p-2 border rounded"
                                            key={index}
                                        >
                                            <img
                                                src={URL.createObjectURL(ni)}
                                                className="object-cover w-16 h-16 rounded"
                                                alt=""
                                            />
                                        </div>
                                    )
                                )}
                            </div>

                            <div className="relative flex flex-wrap items-center gap-3 mt-3">
                                <input
                                    type="file"
                                    id="multi_prod_img"
                                    className="absolute hidden p-1 border"
                                    multiple
                                    accept="image/*"
                                    disabled={remainingOtherImageSlots < 1}
                                    onChange={handleOtherImagesChange}
                                />
                                <label
                                    htmlFor="multi_prod_img"
                                    className={`inline-flex items-center justify-center w-9 h-9 border rounded ${
                                        remainingOtherImageSlots < 1
                                            ? "cursor-not-allowed opacity-50"
                                            : "cursor-pointer"
                                    }`}
                                >
                                    <i className="fas fa-upload"></i>
                                </label>
                                <div className="text-xs leading-5">
                                    You can upload maximum {MAX_OTHER_IMAGES} images. You can add {remainingOtherImageSlots} more.
                                </div>
                                {errors.newImage ? (
                                    <div className="text-xs text-red-500">
                                        {errors.newImage}
                                    </div>
                                ) : null}
                            </div>
                        </SectionInner>
                    </SectionSection>

                    <SectionSection>
                        <SectionHeader
                            title={t("Description")}
                            content={t("Descrive your product as you need.")}
                        />
                        <SectionInner>
                            <InputFile
                                label={t("Description")}
                                labelWidth="250px"
                                error="description"
                                errors={errors}
                            >
                                <main>
                                    {trixReady && (
                                        <trix-toolbar id={`my_toolbar_${inputId}`}></trix-toolbar>
                                    )}
                                    <div className="more-stuff-inbetween"></div>
                                    <input
                                        type="hidden"
                                        name="content"
                                        id={`my_input_${inputId}`}
                                        value={form.data.description}
                                        onChange={() => {}}
                                    />
                                    {trixReady ? (
                                        <trix-editor
                                            ref={editorRef}
                                            toolbar={`my_toolbar_${inputId}`}
                                            input={`my_input_${inputId}`}
                                        ></trix-editor>
                                    ) : (
                                        <textarea
                                            className="w-full border-gray-300 rounded"
                                            id="editor"
                                            rows="10"
                                            value={form.data.description}
                                            onChange={(e) =>
                                                form.setData(
                                                    "description",
                                                    e.target.value
                                                )
                                            }
                                        ></textarea>
                                    )}
                                </main>
                            </InputFile>
                        </SectionInner>
                    </SectionSection>

                    <PrimaryButton>{t("save")}</PrimaryButton>
                </form>
            </Container>
        </AppLayout>
    );
}
