export default function Rating({ value }: { value: number }) {
    return (
        <span
            aria-label={`Rated ${value} out of 10`}
            className="inline-flex items-baseline gap-1 rounded-full bg-amber-400 px-3 py-1 font-black text-stone-950"
        >
            <span>{value.toFixed(1)}</span>
            <span className="text-xs">/10</span>
        </span>
    );
}
