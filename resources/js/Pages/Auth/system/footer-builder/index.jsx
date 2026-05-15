import { Head, useForm } from "@inertiajs/react";
import AppLayout from "../../../../Layouts/App";
import PageHeader from "../../../../components/dashboard/PageHeader";
import PrimaryButton from "../../../../components/PrimaryButton";
import SecondaryButton from "../../../../components/SecondaryButton";
import useTranslation from "../../../../hooks/useTranslation";

function normalizeWidget(widget = {}) {
    return {
        type: widget.type ?? "text",
        content: widget.content ?? "",
        label: widget.label ?? "",
        url: widget.url ?? "",
        icon: widget.icon ?? "",
    };
}

function toArray(value) {
    if (Array.isArray(value)) {
        return value;
    }

    if (value && typeof value === "object") {
        return Object.values(value);
    }

    return [];
}

function normalizeColumn(column = {}) {
    const widgets = toArray(column.widgets).map(normalizeWidget);

    return { widgets };
}

function normalizeSection(section = {}) {
    const rawColumns = toArray(section.columns);
    const columns = rawColumns.length > 0
        ? rawColumns.map(normalizeColumn)
        : [{ widgets: [] }];

    return {
        title: section.title ?? "New Section",
        columns,
    };
}

function normalizeLayout(layoutData) {
    const sections = toArray(layoutData?.sections).map(normalizeSection);

    return { sections };
}

export default function Index({ layoutData }) {
    const { t } = useTranslation();
    const form = useForm({
        layout: normalizeLayout(layoutData),
    });

    const addSection = () => {
        form.setData("layout", {
            ...form.data.layout,
            sections: [
                ...(form.data.layout?.sections ?? []),
                {
                    title: "New Section",
                    columns: [{ widgets: [] }],
                },
            ],
        });
    };

    const addColumn = (sIndex) => {
        form.setData("layout", {
            ...form.data.layout,
            sections: form.data.layout.sections.map((section, index) =>
                index === sIndex
                    ? {
                          ...section,
                          columns: [...section.columns, { widgets: [] }],
                      }
                    : section
            ),
        });
    };

    const addWidget = (sIndex, cIndex, type = "text") => {
        form.setData("layout", {
            ...form.data.layout,
            sections: form.data.layout.sections.map((section, sectionIndex) =>
                sectionIndex === sIndex
                    ? {
                          ...section,
                          columns: section.columns.map((column, columnIndex) =>
                              columnIndex === cIndex
                                  ? {
                                        ...column,
                                        widgets: [
                                            ...column.widgets,
                                            {
                                                type,
                                                content: "",
                                                label: "",
                                                url: "",
                                                icon: "",
                                            },
                                        ],
                                    }
                                  : column
                          ),
                      }
                    : section
            ),
        });
    };

    const updateSectionTitle = (sIndex, value) => {
        form.setData("layout", {
            ...form.data.layout,
            sections: form.data.layout.sections.map((section, index) =>
                index === sIndex ? { ...section, title: value } : section
            ),
        });
    };

    const updateWidget = (sIndex, cIndex, wIndex, key, value) => {
        form.setData("layout", {
            ...form.data.layout,
            sections: form.data.layout.sections.map((section, sectionIndex) =>
                sectionIndex === sIndex
                    ? {
                          ...section,
                          columns: section.columns.map((column, columnIndex) =>
                              columnIndex === cIndex
                                  ? {
                                        ...column,
                                        widgets: column.widgets.map((widget, widgetIndex) =>
                                            widgetIndex === wIndex ? { ...widget, [key]: value } : widget
                                        ),
                                    }
                                  : column
                          ),
                      }
                    : section
            ),
        });
    };

    const save = () => {
        form.post(route("system.footer.builder.save"));
    };

    return (
        <AppLayout
            title={t("Footer Builder")}
            header={<PageHeader>{t("Footer Builder")}</PageHeader>}
        >
            <Head title={t("Footer Builder")} />

            <div>
                <SecondaryButton type="button" onClick={addSection}>{t("+ Add Section")}</SecondaryButton>

                <div className="space-y-6">
                    {(form.data.layout?.sections ?? []).map((section, sIndex) => (
                        <div key={sIndex} className="p-4 border rounded bg-gray-50">
                            <input
                                type="text"
                                value={section.title}
                                onChange={(e) => updateSectionTitle(sIndex, e.target.value)}
                                className="w-full px-2 py-1 mb-2 border"
                                placeholder={t("Section Title")}
                            />

                            <div
                                className="grid gap-4"
                                style={{
                                    gridTemplateColumns: `repeat(${section.columns.length || 1}, minmax(0, 1fr))`,
                                }}
                            >
                                {section.columns.map((col, cIndex) => (
                                    <div key={cIndex} className="p-2 bg-white border rounded">
                                        <h4 className="mb-2 font-semibold">{t("Column")}{cIndex + 1}</h4>

                                        {col.widgets.map((widget, wIndex) => (
                                            <div key={wIndex} className="p-2 mb-2 border rounded">
                                                {widget.type === "text" && (
                                                    <textarea
                                                        value={widget.content}
                                                        onChange={(e) =>
                                                            updateWidget(sIndex, cIndex, wIndex, "content", e.target.value)
                                                        }
                                                        className="w-full px-2 py-1 border"
                                                        placeholder={t("Text...")}
                                                    ></textarea>
                                                )}
                                                {widget.type === "link" && (
                                                    <>
                                                        <input
                                                            value={widget.label}
                                                            onChange={(e) =>
                                                                updateWidget(sIndex, cIndex, wIndex, "label", e.target.value)
                                                            }
                                                            placeholder={t("Link Label")}
                                                            className="w-full px-2 py-1 mb-1 border"
                                                        />
                                                        <input
                                                            value={widget.url}
                                                            onChange={(e) =>
                                                                updateWidget(sIndex, cIndex, wIndex, "url", e.target.value)
                                                            }
                                                            placeholder={t("Link URL")}
                                                            className="w-full px-2 py-1 border"
                                                        />
                                                    </>
                                                )}
                                                {widget.type === "icon" && (
                                                    <>
                                                        <input
                                                            value={widget.icon}
                                                            onChange={(e) =>
                                                                updateWidget(sIndex, cIndex, wIndex, "icon", e.target.value)
                                                            }
                                                            placeholder={t("Icon name (e.g., facebook)")}
                                                            className="w-full px-2 py-1 mb-1 border"
                                                        />
                                                        <input
                                                            value={widget.url}
                                                            onChange={(e) =>
                                                                updateWidget(sIndex, cIndex, wIndex, "url", e.target.value)
                                                            }
                                                            placeholder={t("Icon URL")}
                                                            className="w-full px-2 py-1 border"
                                                        />
                                                    </>
                                                )}
                                            </div>
                                        ))}

                                        <div className="space-x-2">
                                            <button type="button" onClick={() => addWidget(sIndex, cIndex, "text")} className="text-sm text-blue-500">{t("+ Text")}</button>
                                            <button type="button" onClick={() => addWidget(sIndex, cIndex, "link")} className="text-sm text-green-500">{t("+ Link")}</button>
                                            <button type="button" onClick={() => addWidget(sIndex, cIndex, "icon")} className="text-sm text-purple-500">{t("+ Icon")}</button>
                                        </div>
                                    </div>
                                ))}
                            </div>

                            <SecondaryButton type="button" onClick={() => addColumn(sIndex)} className="mt-2 text-green-600">{t("+ Add Column")}</SecondaryButton>
                        </div>
                    ))}
                </div>

                <PrimaryButton type="button" onClick={save} className="px-4 py-2 mt-4 text-white bg-green-600 rounded">{t("Save Footer")}</PrimaryButton>
            </div>
        </AppLayout>
    );
}
