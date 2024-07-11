import os
import fitz  # PyMuPDF

def highlight_keywords_in_pdf(pdf_path, keywords, output_path):
    doc = fitz.open(pdf_path)
    keyword_found = False
    
    for page_num in range(len(doc)):
        page = doc.load_page(page_num)
        for keyword in keywords:
            text_instances = page.search_for(keyword)
            if text_instances:
                keyword_found = True
                for inst in text_instances:
                    highlight = page.add_highlight_annot(inst)
                    highlight.update()
    
    if keyword_found:
        doc.save(output_path, garbage=4, deflate=True)
        print(f"Saved highlighted PDF to {output_path}")
    
    doc.close()
    return keyword_found

def load_keywords(keyword_file):
    with open(keyword_file, 'r') as file:
        keywords = [line.strip() for line in file if line.strip()]
    print(f"Loaded keywords from {keyword_file}: {keywords}")
    return keywords

def process_pdfs(notes_dir, rollno, keywords, output_dir):
    combined_keyword_found = False
    output_index = 1
    
    for pdf_file in os.listdir(notes_dir):
        if pdf_file.endswith('.pdf'):
            pdf_path = os.path.join(notes_dir, pdf_file)
            output_path = os.path.join(output_dir, f"{rollno}_{output_index}.pdf")
            if highlight_keywords_in_pdf(pdf_path, keywords, output_path):
                combined_keyword_found = True
                output_index += 1
    
    if not combined_keyword_found:
        print(f"No keywords found in any PDFs for roll number {rollno}, no output generated.")
    else:
        print(f"Processed and created {output_index - 1} highlighted PDFs for roll number {rollno}")

def main():
    keywords_dir = './keyword'
    notes_dir = './Notes/Common'
    output_dir = './highlight'

    if not os.path.exists(output_dir):
        os.makedirs(output_dir)

    while True:
        for keyword_file in os.listdir(keywords_dir):
            if keyword_file.endswith('.txt'):
                rollno = os.path.splitext(keyword_file)[0]
                keywords = load_keywords(os.path.join(keywords_dir, keyword_file))
                
                if not keywords:
                    print(f"Keyword file {keyword_file} is empty, skipping.")
                    continue

                process_pdfs(notes_dir, rollno, keywords, output_dir)

if __name__ == '__main__':
    main()
