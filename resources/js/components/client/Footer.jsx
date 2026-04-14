import NavLink from "../NavLink";

export default function Footer({ layout }) {
    if (!layout?.sections?.length) return null;

    return (
        <footer className="p-6 text-white bg-gray-900">
            <div
                className="grid gap-6"
                style={{ gridTemplateColumns: `repeat(${layout.sections.length}, minmax(0, 1fr))` }}
            >
                {layout.sections.map((section, sectionIndex) => (
                    <div key={sectionIndex}>
                        <h4 className="mb-2 font-bold">{section.title}</h4>
                        <div
                            className="grid gap-4"
                            style={{ gridTemplateColumns: `repeat(${section.columns?.length || 1}, minmax(0, 1fr))` }}
                        >
                            {(section.columns || []).map((col, colIndex) => (
                                <ul key={colIndex}>
                                    {(col.widgets || []).map((widget, widgetIndex) => {
                                        if (widget.type === "text") {
                                            return <p key={widgetIndex}>{widget.content}</p>;
                                        }
                                        if (widget.type === "link") {
                                            return (
                                                <li key={widgetIndex}>
                                                    <NavLink
                                                        href={widget.url}
                                                        unstyled
                                                        className="hover:underline"
                                                    >
                                                        {widget.label}
                                                    </NavLink>
                                                </li>
                                            );
                                        }
                                        if (widget.type === "icon") {
                                            return (
                                                <NavLink
                                                    key={widgetIndex}
                                                    href={widget.url}
                                                    target="_blank"
                                                    rel="noreferrer"
                                                    unstyled
                                                    className="inline-block mr-2"
                                                >
                                                    <i className={`fab fa-${widget.icon}`}></i>
                                                </NavLink>
                                            );
                                        }
                                        return null;
                                    })}
                                </ul>
                            ))}
                        </div>
                    </div>
                ))}
            </div>
        </footer>
    );
}
