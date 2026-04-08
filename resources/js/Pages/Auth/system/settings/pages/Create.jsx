import { Head, Link, useForm } from "@inertiajs/react";
import { useEffect, useId, useRef, useState } from "react";
import AppLayout from "../../../../../Layouts/App";
import Hr from "../../../../../components/Hr";
import InputField from "../../../../../components/InputField";
import NavLink from "../../../../../components/NavLink";
import NavLinkBtn from "../../../../../components/NavLinkBtn";
import PrimaryButton from "../../../../../components/PrimaryButton";
import TextInput from "../../../../../components/TextInput";
import Container from "../../../../../components/dashboard/Container";
import PageHeader from "../../../../../components/dashboard/PageHeader";
import Section from "../../../../../components/dashboard/section/Section";
import SectionHeader from "../../../../../components/dashboard/section/Header";
import SectionInner from "../../../../../components/dashboard/section/Inner";

function slugify(value) {
    return String(value || "")
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, "")
        .replace(/\s+/g, "-")
        .replace(/-+/g, "-");
}

export default function Create({
    pages = [],
    pageQuery = null,
    pageData = null,
}) {
    const inputId = useId().replace(/:/g, "");
    const editorRef = useRef(null);
    const [trixReady, setTrixReady] = useState(typeof window !== "undefined" && !!window.Trix);
    const [thumbnailPreview, setThumbnailPreview] = useState(null);

    const form = useForm({
        id: pageData?.id ?? "",
        page: pageQuery ?? pageData?.slug ?? "",
        name: pageData?.name ?? "",
        slug: pageData?.slug ?? "",
        title: pageData?.title ?? "",
        keyword: pageData?.keyword ?? "",
        description: pageData?.description ?? "",
        content: pageData?.content ?? "",
        thumbnail: null,
    });

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

        const syncContent = () => {
            form.setData("content", editor.editor?.getDocument()?.toString() ? editor.value : form.data.content);
        };

        const handleChange = (event) => {
            form.setData("content", event.target.value);
        };

        editor.addEventListener("trix-change", handleChange);

        if (form.data.content && editor.editor) {
            editor.editor.loadHTML(form.data.content);
        }

        return () => {
            editor.removeEventListener("trix-change", handleChange);
            syncContent();
        };
    }, [trixReady]);

    useEffect(() => {
        if (!form.data.thumbnail) {
            setThumbnailPreview(null);
            return undefined;
        }

        const url = URL.createObjectURL(form.data.thumbnail);
        setThumbnailPreview(url);

        return () => URL.revokeObjectURL(url);
    }, [form.data.thumbnail]);

    const submit = (e) => {
        e.preventDefault();
        form.post("/dashboard/system/pages/add-new", {
            forceFormData: true,
        });
    };

    const changeName = (e) => {
        const value = e.target.value;
        form.setData("name", value);
        form.setData("slug", slugify(value));
    };

    const currentThumbnail = thumbnailPreview
        ? thumbnailPreview
        : pageData?.thumbnail
          ? `/storage/${pageData.thumbnail}`
          : null;

    return (
        <AppLayout
            title="Create A New Page"
            header={
                <PageHeader>
                    Create A New Page
                    <br />
                    <NavLink href={route("system.pages.index")} className="">
                        <i className="fas fa-angle-left pr-2"></i> Back
                    </NavLink>
                </PageHeader>
            }
        >
            <Head title="Create A New Page" />

            <Container>
                <form onSubmit={submit}>
                    <div className="flex justify-between items-start">
                        <div>
                            <Section className="w-full">
                                <InputField
                                    inputClass="w-full"
                                    label="Page Name"
                                    name="name"
                                    error={form.errors.name}
                                    value={form.data.name}
                                    onChange={changeName}
                                />
                                <div className="flex items-center">
                                    Page URL : https://nolicx.com/pages/
                                    <TextInput
                                        value={form.data.slug}
                                        name="slug"
                                        className="border-0"
                                        onChange={(e) => form.setData("slug", e.target.value)}
                                    />
                                </div>
                            </Section>

                            <Section>
                                <InputField
                                    inputClass="w-full"
                                    label="Page Title"
                                    name="title"
                                    error={form.errors.title}
                                    value={form.data.title}
                                    onChange={(e) => form.setData("title", e.target.value)}
                                />
                                <InputField
                                    inputClass="w-full"
                                    label="Page Keyword"
                                    name="keyword"
                                    error={form.errors.keyword}
                                    value={form.data.keyword}
                                    onChange={(e) => form.setData("keyword", e.target.value)}
                                />
                                <textarea
                                    name="description"
                                    id="description"
                                    placeholder="Description "
                                    className="w-full rounded"
                                    rows="3"
                                    value={form.data.description}
                                    onChange={(e) => form.setData("description", e.target.value)}
                                />
                                <Hr />

                                <div style={{ width: "300px", height: "100px" }}>
                                    {currentThumbnail && (
                                        <img
                                            src={currentThumbnail}
                                            style={{ width: "300px", height: "100px" }}
                                            alt=""
                                            className="border rounded "
                                        />
                                    )}
                                </div>
                                <div className="relative w-full">
                                    <p className="text-xs"> 300 x 100 thumbnail for social media share </p>
                                    <input
                                        type="file"
                                        name="thumbnail"
                                        className="absolute hidden top-0"
                                        id="thumbnail"
                                        onChange={(e) => form.setData("thumbnail", e.target.files?.[0] ?? null)}
                                    />
                                    <label htmlFor="thumbnail">
                                        <i className="fas fa-upload p-2 mt-1 border rounded shadow"></i>
                                    </label>
                                </div>
                                {form.errors.thumbnail && (
                                    <p className="text-red-900"> {form.errors.thumbnail} </p>
                                )}
                                <Hr />
                            </Section>
                        </div>

                        <Section style={{ width: "300px" }}>
                            <SectionHeader
                                title="Other Pages"
                                content="Edit and Update other pages"
                            />

                            <SectionInner>
                                {pages.map((item) => (
                                    <Link
                                        key={item.id}
                                        href={route("system.pages.create", { page: item.slug })}
                                        className="flex justify-between items-center border-b py-2 px-1"
                                    >
                                        <div>
                                            <i className="fas fa-globe pr-2"></i>
                                            {item.name}
                                        </div>
                                        <i className="fas fa-angle-right"></i>
                                    </Link>
                                ))}
                            </SectionInner>
                            <NavLinkBtn href={route("system.pages.create")}>
                                <i className="fas fa-plus pr-2"></i> Page
                            </NavLinkBtn>
                        </Section>
                    </div>

                    <main>
                        {trixReady && <trix-toolbar id={`my_toolbar_${inputId}`}></trix-toolbar>}
                        <div className="more-stuff-inbetween"></div>
                        <input
                            type="hidden"
                            name="content"
                            id={`my_input_${inputId}`}
                            value={form.data.content}
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
                                className="w-full rounded-md shadow-sm border-gray-300"
                                rows="12"
                                value={form.data.content}
                                onChange={(e) => form.setData("content", e.target.value)}
                            />
                        )}
                    </main>
                    <Hr />
                    <PrimaryButton type="submit" disabled={form.processing}>
                        <i className="fas fa-save pr-2"></i> Save & Update
                    </PrimaryButton>
                </form>
            </Container>
        </AppLayout>
    );
}
